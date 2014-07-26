<?php
abstract class MiniME extends NodeBase
	{
	
	private $modules=array();
	final function modules() { return $this->modules; }
	
	function __construct(MiniME $parent=NULL)
		{
		parent::__construct($parent);
//		$this->do_event('node_create');
		}
	
	function get_my_modules() { return array(); } // by default, no modules used.
	
	function call_module_method($module,$method,$args=array())
		{
		// $module::$method() must exist!
/*
		$argstr=implode(',',$args);
		echo "Calling $module::$method($argstr)<br>";
*/
		return call_user_func_array(
			array($module,$method),
			array_merge(array($this),$args)
			);
		}
	
	/*
	Event Handling
	*/
	final function do_event($event)
		{
		$args=func_get_args();
		$event=array_shift($args); // the = is redundant, but hey.... ;)
		$r=array();
		$method="event_$event";
		foreach($this->modules() as $module)
			if(method_exists($module,$method)) 
				$r[$module]=$this->call_module_method($module,$method,$args);
//		if('issue_created'==$event) echo "<pre>".print_r($r,true)."</pre>";
		return $r;
		}

	/*
	"Module Traits" Handling
	*/
	function __call($name,$arguments=array())
		{
		$method="feature_$name";
		$m=$this->modules();
		$found=false;
		for($i=0;$i<count($m)&&!($found=method_exists($m[$i],$method));$i++);
		return ($found?$this->call_module_method($m[$i],$method,$arguments):null);
		}
		
	abstract function create_default_nodes();
		
	final function create_my_nodes()
		{
		$parent=$this->parent();
		if($parent) $this->modules=$parent->modules();
		foreach($this->get_my_modules() as $module)
			$this->modules[]=$module;
			// TODO: Check dependencies / multiples
		$this->do_event('node_init');
		$r=$this->create_default_nodes();
		foreach($this->do_event('node_children') as $module=>$nodes)
			$r=$r+$nodes;//array_merge($nodes,$r); // dont override self-defined
//		foreach($r as $id=>$obj) echo $id."=>".get_class($obj)."<br>"; 
		return $r;
		}
		
	final function equip($name,$value) { $this->$name=$value; }
	
	abstract protected function get_response($redundant=array());
	
	final function leaf_execute($redundant=array())
		{
		$this->do_event('before_response');
		echo $this->get_response($redundant);
		$this->do_event('after_response');
//		if(count($redundant)) print_r($redundant);
		}
		
	/*
	URL Handling
	We assume PATH_INFO is being followed
	*/
	final function node_url()
		{
		if($this->parent())
			return $this->parent()->node_url().'/'.$this->segment();
		else
			return preg_replace('/'.preg_quote($_SERVER['PATH_INFO'],'/').'$/s','',$this->current_url());
		}
		
	final function current_url() 
		{
		$pageURL='http';
		if(isset($_SERVER["HTTPS"])&&($_SERVER["HTTPS"]=="on")) $pageURL.="s";
		$pageURL.="://".$_SERVER["SERVER_NAME"];
		if(($_SERVER["SERVER_PORT"]!="80")&&($_SERVER["SERVER_PORT"]!="443")) $pageURL.=":".$_SERVER["SERVER_PORT"];
 		$pageURL.=strtok($_SERVER["REQUEST_URI"],'?'); // exclude the query string
		return $pageURL;
		}
}
	


