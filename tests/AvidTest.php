<?php

namespace Wubs\Avid;

use Illuminate\Support\Collection;

class AvidTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanBeCreated()
    {
        $ardent = new Avid();
    }

    public function testItCanAddModelToStack()
    {
        $ardent = new Avid();
        $user = new User;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $ardent->add($user);
        $this->assertEquals(<<<EOT
avidItems["User"]=[{"name":"foo","email":"bar@foo.com"}];
EOT
            , $ardent->script()
        );
    }

    public function testItCanAddCollectionToStack()
    {
        $ardent = new Avid();
        $user = new User;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $user2 = new User;
        $user2->name = 'foo2';
        $user2->email = 'bar2@foo.com';

        $ardent->add(new Collection([$user, $user2]));

        $this->assertEquals(<<<EOT
avidItems["User"]=[{"name":"foo","email":"bar@foo.com"},{"name":"foo2","email":"bar2@foo.com"}];
EOT
            , $ardent->script()
        );
    }

    public function testModelNameIsUsedInJavaScriptCode()
    {
        $ardent = new Avid();
        $user = new Home;
        $ardent->add($user);
        $jsCode = $ardent->script();
        $this->assertContains('avidItems["Home"]', $jsCode);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyObjectsExtendingFromModelCanBePushed()
    {
        $ardent = new Avid();
        $ardent->add('');
    }

    public function testItCanHandleMultipleModelTypes()
    {
        $ardent = new Avid();
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
        $ardent->add(collect([$user, $user2, $home]));
        $this->assertEquals(<<<EOT
avidItems["User"]=[{"name":"foo","email":"bar@foo.com"},{"name":"foo2","email":"bar2@foo.com"}];
avidItems["Home"]=[{"id":1,"name":"unknown","user_id":1}];
EOT
            , $ardent->script());
    }

    public function testItCanHandleRelations()
    {
        $ardent = new Avid();
        $user = new User;
        $user->id = 1;
        $user->name = 'foo';
        $user->email = 'bar@foo.com';
        $home = new Home;
        $home->id = 1;
        $home->name = "unknown";
        $home->user_id = 1;
        $home->user = $user;
        $ardent->add($home);
        $this->assertEquals(<<<EOT
avidItems["Home"]=[{"id":1,"name":"unknown","user_id":1,"user":{"id":1,"name":"foo","email":"bar@foo.com"}}];
EOT
            , $ardent->script());
    }

}

class User
{
}

class Home
{
}
