<?php
namespace Triadev\Leopard\Business\Dsl;

use ONGR\ElasticsearchDSL\Suggest\Suggest;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Facade\Leopard;

class Suggestion
{
    /** @var \ONGR\ElasticsearchDSL\Search */
    private $_search;
    
    /** @var ElasticsearchManagerContract */
    private $_manager;
    
    /**
     * Suggestion constructor.
     * @param ElasticsearchManagerContract $manager
     * @param \ONGR\ElasticsearchDSL\Search|null $search
     */
    public function __construct(
        ElasticsearchManagerContract $manager,
        ?\ONGR\ElasticsearchDSL\Search $search = null
    ) {
        $this->_manager = $manager;
        $this->_search = $search ?: new \ONGR\ElasticsearchDSL\Search();
    }
    
    /**
     * To dsl
     *
     * @return array
     */
    public function toDsl(): array
    {
        return $this->_search->toArray();
    }
    
    /**
     * Get
     *
     * @param string $index
     * @return array
     */
    public function get(string $index): array
    {
        return Leopard::suggestStatement([
            'index' => $index,
            'body' => $this->toDsl()
        ]);
    }
    
    /**
     * Term
     *
     * @param string $name
     * @param string $text
     * @param string $field
     * @param array $params
     * @return Suggestion
     */
    public function term(string $name, string $text, string $field, array $params = []): Suggestion
    {
        $this->_search->addSuggest(new Suggest(
            $name,
            'term',
            $text,
            $field,
            $params
        ));
        
        return $this;
    }
    
    /**
     * Phrase
     *
     * @param string $name
     * @param string $text
     * @param string $field
     * @param array $params
     * @return Suggestion
     */
    public function phrase(string $name, string $text, string $field, array $params = []): Suggestion
    {
        $this->_search->addSuggest(new Suggest(
            $name,
            'phrase',
            $text,
            $field,
            $params
        ));
        
        return $this;
    }
    
    /**
     * Term
     *
     * @param string $name
     * @param string $text
     * @param string $field
     * @param array $params
     * @return Suggestion
     */
    public function completion(string $name, string $text, string $field, array $params = []): Suggestion
    {
        $this->_search->addSuggest(new Suggest(
            $name,
            'completion',
            $text,
            $field,
            $params
        ));
        
        return $this;
    }
}
