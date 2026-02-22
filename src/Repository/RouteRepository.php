<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Repository;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symkit\RoutingBundle\Entity\Route;

/**
 * @extends ServiceEntityRepository<Route>
 */
class RouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Route::class)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @return iterable<Route>
     */
    public function findRoutes(): iterable
    {
        // @phpstan-ignore-next-line return.type (Doctrine toIterable() does not expose generic)
        return $this->createQueryBuilder('r')
            ->select('r')
            ->getQuery()
            ->toIterable()
        ;
    }

    /**
     * @return iterable<Route>
     */
    public function findActivatedRoutes(?int $limit = null, ?int $offset = null): iterable
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->andWhere('r.isActive = TRUE')
        ;

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        // @phpstan-ignore-next-line return.type (Doctrine toIterable() does not expose generic)
        return $qb->getQuery()->toIterable();
    }

    public function countActivatedRoutes(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.isActive = TRUE')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return array{count: int, lastUpdate: int}
     */
    public function getRoutingState(): array
    {
        /** @var array{count: int|string|float, lastUpdate: DateTimeInterface|string|null} $result */
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id) as count, MAX(r.updatedAt) as lastUpdate')
            ->getQuery()
            ->getSingleResult();

        $lastUpdate = 0;
        $lastUpdateRaw = $result['lastUpdate'];
        if ($lastUpdateRaw instanceof DateTimeInterface) {
            $lastUpdate = $lastUpdateRaw->getTimestamp();
        } elseif (null !== $lastUpdateRaw && '' !== $lastUpdateRaw) {
            $lastUpdate = (new DateTimeImmutable((string) $lastUpdateRaw))->getTimestamp();
        }

        return [
            'count' => (int) $result['count'],
            'lastUpdate' => $lastUpdate,
        ];
    }

    /**
     * @return iterable<Route>
     */
    public function findForGlobalSearch(string $query, int $limit = 5): iterable
    {
        // @phpstan-ignore-next-line return.type (Doctrine toIterable() does not expose generic)
        return $this->createQueryBuilder('r')
            ->where('r.name LIKE :query OR r.path LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->toIterable()
        ;
    }

    public function isPathTaken(string $path, ?Route $exclude = null): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.path = :path')
            ->setParameter('path', $path)
        ;

        if ($exclude && $exclude->getId()) {
            $qb->andWhere('r.id != :id')
                ->setParameter('id', $exclude->getId())
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function isNameTaken(string $name, ?Route $exclude = null): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.name = :name')
            ->setParameter('name', $name)
        ;

        if ($exclude && $exclude->getId()) {
            $qb->andWhere('r.id != :id')
                ->setParameter('id', $exclude->getId())
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
