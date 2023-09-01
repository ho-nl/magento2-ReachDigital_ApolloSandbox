<?php

declare(strict_types=1);

namespace ReachDigital\ApolloSandbox\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Escaper;

class Index extends Action implements HttpGetActionInterface
{
    private \Magento\Framework\Controller\Result\RawFactory $rawFactory;
    private \Magento\Framework\UrlInterface $urlBuilder;
    private Escaper $escaper;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\RawFactory $rawFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        Escaper $escaper
    ) {
        parent::__construct($context);
        $this->rawFactory = $rawFactory;
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $rawResponse = $this->rawFactory->create();
        $rawResponse->setContents($this->getEmbedScript());
        return $rawResponse;
    }

    private function getEmbedScript(): string
    {
        return '
<html>
    <head>
        <title>Apollo Sandbox</title>
    </head>
    <body style="margin:0;padding:0">
        <div style="width: 100%; height: 100%;" id="embedded-sandbox"></div>
        <script src="https://embeddable-sandbox.cdn.apollographql.com/_latest/embeddable-sandbox.umd.production.min.js"></script>
        <script>
            new window.EmbeddedSandbox({
                target: "#embedded-sandbox",
                initialEndpoint: "' . $this->escaper->escapeUrl($this->getGraphqlUrl()) . '",
                includeCookies: true
            });
        </script>
    </body>
</html>
';
    }

    private function getGraphqlUrl(): string
    {
        return rtrim($this->urlBuilder->getBaseUrl([ '_scope' => 0 ]), '/') . '/graphql';
    }
}
