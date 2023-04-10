<?php
/**
 * Elasticsearch PHP client
 *
 * @link      https://github.com/elastic/elasticsearch-php/
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license   https://www.gnu.org/licenses/lgpl-2.1.html GNU Lesser General Public License, Version 2.1 
 * 
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the Apache 2.0 License or
 * the GNU Lesser General Public License, Version 2.1, at your option.
 * See the LICENSE file in the project root for more information.
 */
declare(strict_types = 1);

namespace Elasticsearch\Namespaces;

use Elasticsearch\Namespaces\AbstractNamespace;

/**
 * Class FleetNamespace
 *
 * NOTE: this file is autogenerated using util/GenerateEndpoints.php
 * and Elasticsearch 7.16.0 (6fc81662312141fe7691d7c1c91b8658ac17aa0d)
 */
class FleetNamespace extends AbstractNamespace
{

    /**
     * Returns the current global checkpoints for an index. This API is design for internal use by the fleet server project.
     *
     * $params['index']            = (string) The name of the index.
     * $params['wait_for_advance'] = (boolean) Whether to wait for the global checkpoint to advance past the specified current checkpoints (Default = true)
     * $params['wait_for_index']   = (boolean) Whether to wait for the target index to exist and all primary shards be active (Default = true)
     * $params['checkpoints']      = (list) Comma separated list of checkpoints (Default = )
     * $params['timeout']          = (time) Timeout to wait for global checkpoint to advance (Default = 30s)
     *
     * @param array $params Associative array of parameters
     * @return array
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/get-global-checkpoints.html
     */
    public function globalCheckpoints(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Fleet\GlobalCheckpoints');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);

        return $this->performRequest($endpoint);
    }
    /**
     * Multi Search API where the search will only be executed after specified checkpoints are available due to a refresh. This API is designed for internal use by the fleet server project.
     *
     * $params['index'] = (string) The index name to use as the default
     * $params['body']  = (array) The request definitions (metadata-fleet search request definition pairs), separated by newlines (Required)
     *
     * @param array $params Associative array of parameters
     * @return array
     *
     * @note This API is EXPERIMENTAL and may be changed or removed completely in a future release
     *
     */
    public function msearch(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');
        $body = $this->extractArgument($params, 'body');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Fleet\Msearch');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);
        $endpoint->setBody($body);

        return $this->performRequest($endpoint);
    }
    /**
     * Search API where the search will only be executed after specified checkpoints are available due to a refresh. This API is designed for internal use by the fleet server project.
     *
     * $params['index']                        = (string) The index name to search.
     * $params['wait_for_checkpoints']         = (list) Comma separated list of checkpoints, one per shard (Default = )
     * $params['wait_for_checkpoints_timeout'] = (time) Explicit wait_for_checkpoints timeout
     * $params['allow_partial_search_results'] = (boolean) Indicate if an error should be returned if there is a partial search failure or timeout (Default = true)
     * $params['body']                         = (array) The search definition using the Query DSL
     *
     * @param array $params Associative array of parameters
     * @return array
     *
     * @note This API is EXPERIMENTAL and may be changed or removed completely in a future release
     *
     */
    public function search(array $params = [])
    {
        $index = $this->extractArgument($params, 'index');
        $body = $this->extractArgument($params, 'body');

        $endpointBuilder = $this->endpoints;
        $endpoint = $endpointBuilder('Fleet\Search');
        $endpoint->setParams($params);
        $endpoint->setIndex($index);
        $endpoint->setBody($body);

        return $this->performRequest($endpoint);
    }
}
