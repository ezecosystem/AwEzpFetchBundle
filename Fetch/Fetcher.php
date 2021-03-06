<?php

/**
 * This file is part of AwEzpFetchBundle
 *
 * @author    Mohamed Karnichi <mka@amiralweb.com>
 * @copyright 2013 Amiral Web
 * @link      http://www.amiralweb.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aw\Ezp\FetchBundle\Fetch;
use Aw\Ezp\FetchBundle\Fetch\Processing\Processor;
use eZ\Publish\API\Repository\Repository;

class Fetcher
{
    protected $repository;
    protected $queryProcessor;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->queryProcessor = new Processor();
    }

    /**
     * Finds content objects for the given query.
     *
     * @param mixed $queryInput CQL query string or query parameters array
     *
     * @param array $fieldFilters - a map of filters for the returned fields.
     *         (see eZ\Publish\API\Repository\SearchService)
     * @param boolean $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *         (see eZ\Publish\API\Repository\SearchService)
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function fetch($queryInput, array $fieldFilters = array(), $filterOnUserPermissions = true)
    {
        $query = $this->processQuery($queryInput)->build();

        return $this->repository->getSearchService()->findContent($query, $fieldFilters, $filterOnUserPermissions);
    }

    /**
     * Returns preparedFetcher for the given query input.
     *
     * @param mixed $queryInput CQL query string or query parameters array
     *
     * @return \Aw\Ezp\FetchBundle\Fetch\PreparedFetcher
     */
    public function prepare($queryInput)
    {
        $queryBuilder = $this->processQuery($queryInput);

        return new PreparedFetcher($queryBuilder, $this->repository);
    }

    protected function processQuery($queryInput)
    {
        return $this->queryProcessor->process($queryInput);
    }
}
