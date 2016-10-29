<?php

namespace Wubs\Avid;

use Illuminate\Support\Collection;

class AvidTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanBeCreated()
    {
        $avid = new Avid();
    }

    public function testItCanAddModelToStack()
    {
        $avid = new Avid();
        $user = new User;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $avid->add($user);
        $this->assertEquals(<<<EOT
avidItems["user"]=[{"name":"foo","email":"bar@foo.com"}];
EOT
            , $avid->script()
        );
    }

    public function testItCanAddCollectionToStack()
    {
        $avid = new Avid();
        $user = new User;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $user2 = new User;
        $user2->name = 'foo2';
        $user2->email = 'bar2@foo.com';

        $avid->add(new Collection([$user, $user2]));

        $this->assertEquals(<<<EOT
avidItems["user"]=[{"name":"foo","email":"bar@foo.com"},{"name":"foo2","email":"bar2@foo.com"}];
EOT
            , $avid->script()
        );
    }

    public function testModelNameIsUsedInJavaScriptCode()
    {
        $avid = new Avid();
        $user = new Home;
        $avid->add($user);
        $jsCode = $avid->script();
        $this->assertContains('avidItems["home"]', $jsCode);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyObjectsExtendingFromModelCanBePushed()
    {
        $avid = new Avid();
        $avid->add('');
    }

    public function testItCanHandleMultipleModelTypes()
    {
        $avid = new Avid();
        $user = new User;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $user2 = new User;
        $user2->name = 'foo2';
        $user2->email = 'bar2@foo.com';
        $home = new Home;
        $home->id = 1;
        $home->name = "unknown";
        $home->user_id = 1;
        $avid->add(collect([$user, $user2, $home]));
        $this->assertEquals(<<<EOT
avidItems["user"]=[{"name":"foo","email":"bar@foo.com"},{"name":"foo2","email":"bar2@foo.com"}];
avidItems["home"]=[{"id":1,"name":"unknown","user_id":1}];
EOT
            , $avid->script());
    }

    public function testItCanHandleRelations()
    {
        $avid = new Avid();
        $user = new User;
        $user->id = 1;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $home = new Home;
        $home->id = 1;
        $home->name = "unknown";
        $home->user_id = 1;
        $home->user = $user;
        $avid->add($home);
        $this->assertEquals(<<<EOT
avidItems["home"]=[{"id":1,"name":"unknown","user_id":1,"user":{"id":1,"name":"foo","email":"bar@foo.com"}}];
EOT
            , $avid->script());
    }

    public function testItCanAddANamedKey()
    {
        $avid = new Avid();
        $user = new User;
        $user->id = 1;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $home = new Home;
        $home->id = 1;
        $home->name = "unknown";
        $home->user_id = 1;
        $home->user = $user;
        $avid->add($home, 'homes');
        $this->assertEquals(<<<EOT
avidItems["homes"]=[{"id":1,"name":"unknown","user_id":1,"user":{"id":1,"name":"foo","email":"bar@foo.com"}}];
EOT
            , $avid->script());
    }
}

class User
{
}

class Home
{
}
