<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Doctrine\Form\ChoiceList;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\ChoiceList\AjaxORMQueryBuilderLoader;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Doctrine\DBAL\Connection;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxORMQueryBuilderLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function getIdentityTypes()
    {
        return array(
            array('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleStringIdEntity', Connection::PARAM_STR_ARRAY),
            array('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', Connection::PARAM_INT_ARRAY),
            array('Sonatra\Bundle\FormExtensionsBundle\Tests\Doctrine\Form\Fixtures\SingleGuidIdEntity', Connection::PARAM_STR_ARRAY),
        );
    }

    /**
     * @dataProvider getIdentityTypes
     *
     * @param string $className
     * @param int    $expectedType
     */
    public function testCheckIdentifierType($className, $expectedType)
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(array('setParameter', 'getResult', 'getSql', '_doExecute'))
            ->getMock();

        $query->expects($this->once())
            ->method('setParameter')
            ->with('AjaxORMQueryBuilderLoader_getEntitiesByIds_id', array(1, 2), $expectedType)
            ->willReturn($query);

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs(array($em))
            ->setMethods(array('getQuery'))
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $qb->select('e')
            ->from($className, 'e');

        $loader = new AjaxORMQueryBuilderLoader($qb);
        $loader->getEntitiesByIds('id', array(1, 2));
    }

    public function testFilterNonIntegerValues()
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(array('setParameter', 'getResult', 'getSql', '_doExecute'))
            ->getMock();

        $query->expects($this->once())
            ->method('setParameter')
            ->with('AjaxORMQueryBuilderLoader_getEntitiesByIds_id', array(1, 2, 3), Connection::PARAM_INT_ARRAY)
            ->willReturn($query);

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs(array($em))
            ->setMethods(array('getQuery'))
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new AjaxORMQueryBuilderLoader($qb);
        $loader->getEntitiesByIds('id', array(1, '', 2, 3, 'foo'));
    }

    public function testSetSearch()
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs(array($em))
            ->setMethods(array('getQuery'))
            ->getMock();
        $loader = new AjaxORMQueryBuilderLoader($qb);
        $loader->setSearch('test', 'foo');
    }

    public function testGetEntities()
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(array('getResult', 'getSql', '_doExecute'))
            ->getMock();

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs(array($em))
            ->setMethods(array('getQuery'))
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new AjaxORMQueryBuilderLoader($qb);
        $loader->getEntities();
    }

    public function testGetPaginatedEntities()
    {
        $em = $this->initEntityManager();
        $qb = new QueryBuilder($em);

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new AjaxORMQueryBuilderLoader($qb);
        $loader->getPaginatedEntities(10, 1);
    }

    public function testGetSize()
    {
        $em = $this->initEntityManager();
        $qb = new QueryBuilder($em);

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new AjaxORMQueryBuilderLoader($qb);
        $loader->getSize();
    }

    /**
     * Init the doctrine entity manager.
     *
     * @return EntityManagerInterface
     */
    protected function initEntityManager()
    {
        $em = DoctrineTestHelper::createTestEntityManager();
        $schemaTool = new SchemaTool($em);
        $classes = array(
            $em->getClassMetadata('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity'),
        );

        try {
            $schemaTool->dropSchema($classes);
        } catch (\Exception $e) {
        }

        try {
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
        }

        return $em;
    }
}
