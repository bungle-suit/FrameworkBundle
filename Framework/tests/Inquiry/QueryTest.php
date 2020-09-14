<?php
declare(strict_types=1);

namespace Bungle\Framework\Tests\Inquiry;

use ArrayIterator;
use Bungle\Framework\Inquiry\Builder;
use Bungle\Framework\Inquiry\ColumnMeta;
use Bungle\Framework\Inquiry\Query;
use Bungle\Framework\Inquiry\QueryParams;
use Bungle\Framework\Inquiry\QueryStepInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\PropertyInfo\Type;

class QueryTest extends MockeryTestCase
{
    /** @var EntityManagerInterface|Mockery\MockInterface */
    private $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = Mockery::mock(EntityManagerInterface::class);
    }

    public function testQuery(): void
    {
        $qb = Mockery::mock(QueryBuilder::class);
        $this->em->expects('createQueryBuilder')->andReturn($qb);
        $dqlQuery = Mockery::mock(AbstractQuery::class);
        $qb->expects('getQuery')->andReturn($dqlQuery);
        $dqlQuery->expects('iterate')->with(null, AbstractQuery::HYDRATE_ARRAY)->andReturn(
            new ArrayIterator(
                [
                    new ArrayIterator([['line1'], ['line2']]),
                    new ArrayIterator([['line3']]),
                    new ArrayIterator([]),
                ]
            ),
        );

        $params = new QueryParams(0, []);
        $step1 = Mockery::mock(QueryStepInterface::class);
        $step2 = Mockery::mock(QueryStepInterface::class);
        $step1->expects('__invoke')->with(
            Mockery::on(
                fn(Builder $builder) => $builder->getQueryParams() === $params && $builder->getQueryBuilder() === $qb
            )
        );
        $step2->expects('__invoke')->with(Mockery::type(Builder::class));
        $col1 = new ColumnMeta('[id]', 'id', new Type(Type::BUILTIN_TYPE_INT));
        $q = new Query($this->em, [
            $step1,
            $step2,
            function (Builder $builder) use ($col1) {
                $builder->addColumn($col1, 'foo');
            },
        ]);

        self::assertEquals([['line1'], ['line2'], ['line3']], iterator_to_array($q->query($params), false));
        self::assertEquals(['foo' => $col1], $q->getColumns());
    }

    public function testPagedQuery(): void
    {
        $qb1 = Mockery::mock(QueryBuilder::class);
        $qb2 = Mockery::mock(QueryBuilder::class);
        $this->em->expects('createQueryBuilder')->andReturn($qb1, $qb2)->twice();
        $dqlQuery1 = Mockery::mock(AbstractQuery::class);
        $dqlQuery2 = Mockery::mock(AbstractQuery::class);
        $qb1->expects('getQuery')->andReturn($dqlQuery1);
        $qb2->expects('getQuery')->andReturn($dqlQuery2);
        $dqlQuery1->expects('iterate')->with(null, AbstractQuery::HYDRATE_ARRAY)->andReturn(
            new ArrayIterator([new ArrayIterator([['line1'], ['line2']])]),
        );
        $dqlQuery2->expects('execute')->with(null, AbstractQuery::HYDRATE_SINGLE_SCALAR)->andReturn(2);

        $isBuildForCounts = [];
        $params = new QueryParams(0, []);
        $col1 = new ColumnMeta('[id]', 'id', new Type(Type::BUILTIN_TYPE_INT));
        $q = new Query($this->em, [
            function (Builder $builder) use ($col1, &$isBuildForCounts) {
                $isBuildForCounts[] = $builder->isBuildForCount();
                if (!$builder->isBuildForCount()) {
                    $builder->addColumn($col1, 'foo');
                }
            },
        ]);

        $pagedData = $q->pagedQuery($params);
        self::assertEquals(2, $pagedData->getCount());
        self::assertEquals([['line1'], ['line2']], $pagedData->getData());
        self::assertEquals([false, true], $isBuildForCounts);
        self::assertEquals(['foo' => $col1], $q->getColumns());
    }
}