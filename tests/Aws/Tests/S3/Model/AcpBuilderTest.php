<?php

namespace Aws\Tests\S3\Model;

use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Permission;
use Aws\S3\Enum\Group;

/**
 * @covers Aws\S3\Model\AcpBuilder
 */
class AcpBuilderTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testCanSetOwner()
    {
        $builder = AcpBuilder::newInstance();
        $this->assertSame($builder, $builder->setOwner('1234567890'));
        $this->assertInstanceOf(
            'Aws\S3\Model\Grantee',
            $this->readAttribute($builder, 'owner')
        );
    }

    public function testCanAddUserGrant()
    {
        $builder = AcpBuilder::newInstance();
        $after = $builder->addGrantForUser(Permission::READ, '12345');
        $this->assertSame($builder, $after);

        $grants = $this->readAttribute($builder, 'grants');
        $this->assertInstanceOf('Aws\S3\Model\Grant', $grants[0]);
        $this->assertTrue($grants[0]->getGrantee()->isCanonicalUser());
    }

    public function testCanAddEmailGrant()
    {
        $builder = AcpBuilder::newInstance();
        $after = $builder->addGrantForEmail(Permission::READ, 'foo@example.com');
        $this->assertSame($builder, $after);

        $grants = $this->readAttribute($builder, 'grants');
        $this->assertInstanceOf('Aws\S3\Model\Grant', $grants[0]);
        $this->assertTrue($grants[0]->getGrantee()->isAmazonCustomerByEmail());
    }

    public function testCanAddGroupGrant()
    {
        $builder = AcpBuilder::newInstance();
        $after = $builder->addGrantForGroup(Permission::READ, Group::ALL_USERS);
        $this->assertSame($builder, $after);

        $grants = $this->readAttribute($builder, 'grants');
        $this->assertInstanceOf('Aws\S3\Model\Grant', $grants[0]);
        $this->assertTrue($grants[0]->getGrantee()->isGroup());
    }

    public function testCanBuildAnAcp()
    {
        $acl = AcpBuilder::newInstance()->setOwner('1234567890')
            ->addGrantForEmail(Permission::READ, 'foo@example.com')
            ->build();

        $this->assertInstanceOf('Aws\S3\Model\Acp', $acl);
    }
}