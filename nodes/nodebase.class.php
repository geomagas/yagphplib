<?php
abstract class NodeBase extends Tree
	{
	
	private $segment;
	final function segment() { return $this->segment; }
	
	abstract function create_my_nodes();
	
	final function traverse(array $path,$seg='')
		{
		$this->segment=$seg;
		foreach($this->create_my_nodes() as $key=>$node) $this->add_node($key,$node);
		$nodes=$this->nodes();
		if(($i=array_shift($path))&&isset($nodes[$i])&&($node=$nodes[$i]))  
			$node->traverse($path,$i); // there is a child named $i
		else // reached the end of the path, or the remaining cannot be resolved
			{
			array_unshift($path,$i);
			$this->leaf_execute($path);
			}
		}
		
	abstract function leaf_execute();
	
	}
