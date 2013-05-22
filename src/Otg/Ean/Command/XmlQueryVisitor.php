<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Command;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Service\Description\Parameter;
use Guzzle\Service\Command\LocationVisitor\Request\XmlVisitor;

/**
 * Serializes XML as a query string parameter
 *
 * @package Otg\Ean\Command
 */
class XmlQueryVisitor extends XmlVisitor
{

    /**
     * {@inheritdoc}
     */
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $xml = isset($this->data[$command])
            ? $this->data[$command]
            : $this->createRootElement($command->getOperation());
        $this->addXml($xml, $param, $value);
        $this->data[$command] = $xml;
    }

    /**
     * {@inheritdoc}
     */
    public function after(CommandInterface $command, RequestInterface $request)
    {
        $xml = null;

        // If data was found that needs to be serialized, then do so
        if (isset($this->data[$command])) {
            $xml = $this->data[$command]->asXML();
            unset($this->data[$command]);
        } else {
            // Check if XML should always be sent for the command
            $operation = $command->getOperation();
            if ($operation->getData('xmlAllowEmpty')) {
                $xml = $this->createRootElement($operation)->asXML();

            }
        }

        if ($xml) {
            $request->getQuery()->set('xml', $this->trimXml($xml));
        }
    }

    /**
     * Removes the declaration <?xml version="1.0"?> and trailing whitespace
     * @param $string
     * @return string
     */
    protected function trimXml($string)
    {
        return trim(substr($string, 22));
    }
}