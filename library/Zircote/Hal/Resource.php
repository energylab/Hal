<?php


class Zircote_Hal_Resource extends Zircote_Hal_AbstractHal
{
    protected $_links = array();
    protected $_data = array();
    protected $_embedded = array();
    public function __construct($href, $name = null)
    {
        parent::__construct($href, 'self', $name = null);
        $self = new Zircote_Hal_Link(
            $this->getHref(), $this->getRel(), $this->getName()
        );
        $this->setLink($self);
    }
    /**
     * @return Zircote_Hal_Link
     */
    public function getSelf()
    {
        return $this->_links['self'];
    }
    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }
    /**
     *
     * @param Zircote_Hal_Link $link
     * @return Zircote_Hal_Resource
     */
    public function setLink(Zircote_Hal_Link $link)
    {
        $this->_links[$link->getRel()] = $link;
        return $this;
    }
    /**
     *
     * @param array $data
     * @return Zircote_Hal_Resource
     */
    public function setData($rel, $data = null)
    {
        if(is_array($rel) && null === $data){
            foreach ($rel as $k => $v) {
                $this->_data[$k] = $v;
            }
        } else {
            $this->_data[$rel] = $data;
        }
        return $this;
    }
    /**
     *
     * @param Zircote_Hal_Resource $resource
     * @return Zircote_Hal_Resource
     */
    public function setEmbedded($rel,Zircote_Hal_Resource $resource, $singular = false)
    {
        if($singular){
            $this->_embedded[$rel] = $resource;
        } else {
            $this->_embedded[$rel][] = $resource;
        }
        return $this;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        $data = array();
        foreach ($this->_links as $rel => $link) {
            $data['_links'][$rel] = $link->toArray();
        }
        foreach ($this->_data as $key => $value) {
            $data[$key] = $value;
        }
        foreach ($this->_embedded as $rel => $embed) {
            $data['_embedded'][$rel] = $this->_recurseEmbedded($embed);
        }
        return $data;
    }
    /**
     *
     * @param mixed $embeded
     */
    protected function _recurseEmbedded($embeded)
    {
        $result = array();
        if($embeded instanceof  self){
            $result = $embeded->toArray();
        } else {
            foreach ($embeded as $embed) {
                if($embed instanceof self){
                    $result[] = $embed->toArray();
                }
            }
        }
        return $result;
    }
    public function __toJson()
    {
        return json_encode($this->toArray());
    }
    public function __toString()
    {
        return $this->__toJson();
    }
}