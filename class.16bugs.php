<?
/****************************************/
/* PHP
	 Copyright 2008 Red Tettemer.
	 redtettemer.com
	 
	 @langversion PHP 5.0
	 
	 @author 	J Marziani
	 @contact 	jmarziani@redtettemer.com
	 @since 	05.11.2008
	 
	 --
	 @revisions
	 none so far, champ.
	 
*/

class SixteenBugs {
	
	protected $c, $co, $u, $t, $baseurl, $h;
	// curl, company, username, token, base url, header
	
	public function __construct($company, $username, $token, $headers=false) {
		$this->co = $company;
	    $this->u = $username;
	    $this->t = $token;
		$this->h = $headers;
		$this->baseurl = "https://".$company.".16bugs.com";
		//	
  	}
  
	public function createBug($projectID, $xml){
		$this->c = curl_init();
		$this->setBaseOps();
		curl_setopt($this->c, CURLOPT_URL, $this->baseurl."/projects/".$projectID."/bugs");
		//curl_setopt ($this->c, CURLOPT_HTTPGET, 1);
		curl_setopt ($this->c, CURLOPT_POST, 1);
		curl_setopt ($this->c, CURLOPT_POSTFIELDS, $xml);
		//
		$result = curl_exec($this->c);
		curl_close($this->c);
		return $result;
	}
	
	public function getBugs($projectID, $bugID=false){
		$this->c = curl_init();
		$this->setBaseOps();
		$a = (!$bugID)? "" : "/".$bugID;
		curl_setopt($this->c, CURLOPT_URL, $this->baseurl."/projects/".$projectID."/bugs".$a);
		curl_setopt ($this->c, CURLOPT_HTTPGET, 1);
		$result = curl_exec($this->c);
		curl_close($this->c);
		return $result;
	}
	
	public function getCategories($projectID, $categoryID = false){
		$this->c = curl_init();
		$this->setBaseOps();
		$a = (!$categoryID)? "" : "/".categoryID;
		curl_setopt($this->c, CURLOPT_URL, $this->baseurl."/projects/".$projectID."/categories".$a);
		curl_setopt ($this->c, CURLOPT_HTTPGET, 1);
		$result = curl_exec($this->c);
		curl_close($this->c);
		return $result;
	
	}
	
	public function getProjects($projectID = false){
		$this->c = curl_init();
		$this->setBaseOps();
		$a = (!$projectID)? "" : "/".$projectID;
		curl_setopt($this->c, CURLOPT_URL, $this->baseurl."/projects".$a);
		curl_setopt ($this->c, CURLOPT_HTTPGET, 1);
		$result = curl_exec($this->c);
		curl_close($this->c);
		return $result;
	}
	
	// returns just the list of projects and their IDs.  Meant to be more lightweight than getPorjects
	public function getProjectList()
	{
		$this->c = curl_init();
		$this->setBaseOps();
		curl_setopt($this->c, CURLOPT_URL, $this->baseurl."/projects");
		curl_setopt ($this->c, CURLOPT_HTTPGET, 1);
		$result = curl_exec($this->c);
		curl_close($this->c);
		
		$xml = new SimpleXMLElement($result); // the whole shebang from 16bugs
		$return = new SimpleXMLElement('<projects type="array"></projects>'); // what we're returning
		foreach ($xml->children() as $child)
		{
			$p = $return->addChild($child->getName());
			foreach ($child->children() as $n)
			{
				if($n->getName() == "id" || $n->getName() == "name")
				{
					$t = ($n->getName() == "name")? "label" : "id";
					$p->addChild($t, $n);
				}
			}
		}
		echo $return->asXML();
		return $return;
	}
	
	// setBaseOps - Sets the common required element to connect to 16Bugs through curl
	private function setBaseOps()
	{
		
		curl_setopt($this->c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->c, CURLOPT_HEADER, $this->h);
		curl_setopt($this->c, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
		curl_setopt($this->c,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->c, CURLOPT_USERPWD, $this->u.":".$this->t);
	}

}
?>