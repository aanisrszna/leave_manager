<?php

namespace Microsoft\Graph\Generated\Security\ThreatIntelligence\SslCertificates\Item\RelatedHosts;

use Exception;
use Http\Promise\Promise;
use Microsoft\Graph\Generated\Models\ODataErrors\ODataError;
use Microsoft\Graph\Generated\Models\Security\HostCollectionResponse;
use Microsoft\Graph\Generated\Security\ThreatIntelligence\SslCertificates\Item\RelatedHosts\Count\CountRequestBuilder;
use Microsoft\Graph\Generated\Security\ThreatIntelligence\SslCertificates\Item\RelatedHosts\Item\HostItemRequestBuilder;
use Microsoft\Kiota\Abstractions\BaseRequestBuilder;
use Microsoft\Kiota\Abstractions\HttpMethod;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\RequestInformation;

/**
 * Provides operations to manage the relatedHosts property of the microsoft.graph.security.sslCertificate entity.
*/
class RelatedHostsRequestBuilder extends BaseRequestBuilder 
{
    /**
     * Provides operations to count the resources in the collection.
    */
    public function count(): CountRequestBuilder {
        return new CountRequestBuilder($this->pathParameters, $this->requestAdapter);
    }
    
    /**
     * Provides operations to manage the relatedHosts property of the microsoft.graph.security.sslCertificate entity.
     * @param string $hostId The unique identifier of host
     * @return HostItemRequestBuilder
    */
    public function byHostId(string $hostId): HostItemRequestBuilder {
        $urlTplParams = $this->pathParameters;
        $urlTplParams['host%2Did'] = $hostId;
        return new HostItemRequestBuilder($urlTplParams, $this->requestAdapter);
    }

    /**
     * Instantiates a new RelatedHostsRequestBuilder and sets the default values.
     * @param array<string, mixed>|string $pathParametersOrRawUrl Path parameters for the request or a String representing the raw URL.
     * @param RequestAdapter $requestAdapter The request adapter to use to execute the requests.
    */
    public function __construct($pathParametersOrRawUrl, RequestAdapter $requestAdapter) {
        parent::__construct($requestAdapter, [], '{+baseurl}/security/threatIntelligence/sslCertificates/{sslCertificate%2Did}/relatedHosts{?%24count,%24expand,%24filter,%24orderby,%24search,%24select,%24skip,%24top}');
        if (is_array($pathParametersOrRawUrl)) {
            $this->pathParameters = $pathParametersOrRawUrl;
        } else {
            $this->pathParameters = ['request-raw-url' => $pathParametersOrRawUrl];
        }
    }

    /**
     * Get a list of related host resources associated with an sslCertificate.
     * @param RelatedHostsRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return Promise<HostCollectionResponse|null>
     * @throws Exception
     * @link https://learn.microsoft.com/graph/api/security-sslcertificate-list-relatedhosts?view=graph-rest-1.0 Find more info here
    */
    public function get(?RelatedHostsRequestBuilderGetRequestConfiguration $requestConfiguration = null): Promise {
        $requestInfo = $this->toGetRequestInformation($requestConfiguration);
        $errorMappings = [
                'XXX' => [ODataError::class, 'createFromDiscriminatorValue'],
        ];
        return $this->requestAdapter->sendAsync($requestInfo, [HostCollectionResponse::class, 'createFromDiscriminatorValue'], $errorMappings);
    }

    /**
     * Get a list of related host resources associated with an sslCertificate.
     * @param RelatedHostsRequestBuilderGetRequestConfiguration|null $requestConfiguration Configuration for the request such as headers, query parameters, and middleware options.
     * @return RequestInformation
    */
    public function toGetRequestInformation(?RelatedHostsRequestBuilderGetRequestConfiguration $requestConfiguration = null): RequestInformation {
        $requestInfo = new RequestInformation();
        $requestInfo->urlTemplate = $this->urlTemplate;
        $requestInfo->pathParameters = $this->pathParameters;
        $requestInfo->httpMethod = HttpMethod::GET;
        if ($requestConfiguration !== null) {
            $requestInfo->addHeaders($requestConfiguration->headers);
            if ($requestConfiguration->queryParameters !== null) {
                $requestInfo->setQueryParameters($requestConfiguration->queryParameters);
            }
            $requestInfo->addRequestOptions(...$requestConfiguration->options);
        }
        $requestInfo->tryAddHeader('Accept', "application/json");
        return $requestInfo;
    }

    /**
     * Returns a request builder with the provided arbitrary URL. Using this method means any other path or query parameters are ignored.
     * @param string $rawUrl The raw URL to use for the request builder.
     * @return RelatedHostsRequestBuilder
    */
    public function withUrl(string $rawUrl): RelatedHostsRequestBuilder {
        return new RelatedHostsRequestBuilder($rawUrl, $this->requestAdapter);
    }

}
