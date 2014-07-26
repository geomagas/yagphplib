<?php
class Tree
	{

	private $parent=null;
	private $nodes=array();
	final function nodes() { return $this->nodes; }
	final function add_node($key,Tree $value) { $this->nodes[$key]=$value; }
	final function parent() { return $this->parent; }
	final function root() { return ($this->parent?$this->parent->root():$this); }
	function __construct(Tree $parent=NULL) 
		{ 
		$this->parent=$parent; 
//		if($this->parent) $this->parent->add_node($this);
		}

	}
