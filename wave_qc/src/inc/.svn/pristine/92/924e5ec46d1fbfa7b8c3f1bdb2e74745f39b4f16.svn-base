<?php
/**
 * CMS: Sanitiser
 *
 *  Description
 *
 * @package    CMS
 * @copyright  Copyright (c) 2012-2015 5Flow
 * @version $Rev: 8307 $
 * @date $Date: 2015-04-07 15:50:22 +0100 (Tue, 07 Apr 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Cms_Sanitiser extends CCor_Obj {
	
	protected $mSwords = array();
	
	/**
	 * Function used to clean a string
	 * @param string $aComment A comment entered by the user
	 * @param string $aLang A comment entered by the user
	 * @param string $aTyp Optional parameter for returning a string instead of matching tokens
	 * @return Array<string, int> If no type supplied individual token and its match rating
	 * @return Array<string, int> If type supplied a string and the size of the string
	 */
	public function sanitise($aComment, $aLang, $aTyp = FALSE) {
	  $lDefault = CCor_Cfg::get("masterlanguage", "EN");
  	  $lNonLang = array('');
  	  $lLangOri = $lLang = $aLang;
  	  $lLang = (in_array($lLang, $lNonLang)) ? $lDefault : $lLang;
  	  
  	  $this -> mSwords = $this -> relevantStopwords($lLang);
  	  $lStopTokens = $lSanTokens = array();
  	  $lComment = trim(strip_tags($aComment));
  	
  	  $lTokens = (strpos($lComment, " ") > -1 ? explode(" ", $lComment) : array($lComment));					
  	  foreach ($lTokens as $lToken) {
  		$lRes = $this -> removeStops($lToken);
  		if ($lRes != "")
  			$lStopTokens[] = $lRes;
  	  }
  	
  	  $lStopTokens = array_unique($lStopTokens); 
  	  foreach ($lStopTokens as $lStop) {
  		$lSanTokens[] = $this -> stemToken($lStop, $lLang);
  	  }
  	
  	  if ($aTyp) {
  		return implode(" ", $lSanTokens);
  	  } else {
  		$lSuggs = $this -> weightedScore($lSanTokens, $lLangOri);
  		
  		return $this -> sortArray($lSuggs, 'matchScore');
  	  }
	}

    /**
    * Function to convert a token to lowercase, remove whitespace, remove punctuation and check to see if it is 
    * in a stop list.
    * @param string $aToken token to clean.
    * @return A cleaned token or blank 
    */
    protected function removeStops($aToken) {
  	  $lToken = strtolower(utf8_decode($aToken));
  	  $lToken = trim(utf8_encode($lToken));
  	  $lToken = preg_replace("#[[:punct:]]#", "", $lToken);
  	
  	  return (in_array($lToken, $this -> mSwords)) ? "" : $lToken;
    }
    
    /**
     * Function to stem any given token. E.g. Running is stemmed to run.
     * @param string $aToken token to stem.
     * @param string $aLang string language.
     * @return The stemmed token
     */
    protected function stemToken($aToken, $aLang) {
      $lLang = $aLang;
      $lToken = $aToken;
      
  	  if(function_exists('stem')){
    	switch($aLang) {
    		case "DA":
    			return stem($lToken, STEM_DANISH);
    		case "ND":
    			return stem($lToken, STEM_DUTCH);
    		case "EN":
    			return stem($lToken, STEM_ENGLISH);
    		case "FN":
    			return stem($lToken, STEM_FINNISH);
    		case "FR":
    			return stem($lToken, STEM_FRENCH);
    		case "DE":
    			return stem($lToken, STEM_GERMAN);
    		case "IT":
    			return stem($lToken, STEM_ITALIAN);
    		case "NO":
    			return stem($lToken, STEM_NORWEGIAN);
    		case "PO":
    			return stem($lToken, STEM_PORTUGUESE);
    		case "ES":
    			return stem($lToken, STEM_SPANISH);
    		case "SW":
    			return stem($lToken, STEM_SWEDISH);
    		default:
    			return stem($lToken, STEM_ENGLISH);
    	}
  	  } else {
  		return $lToken;
  	  }
    }
    
    /**
     * Function to calculate match likeliness.
     * @param Array[string] $aTokens list of sanitized tokens
     * @param string $aLang string language
     * @return $suggestions A list of possible matching phrases
    */
    protected function weightedScore($aTokens, $aLang) {
      try {
	    $lSuggs = array();
	    $lTotal = sizeof($aTokens);
	    if ($lTotal == 0) return FALSE;
	    
      $lSql = 'SELECT b.`content_id`, b.`parent_id`, c.`content`, c.`tokens`, c.`status`, d.`category`, a.`language`, a.`maxver` as ver ';
      $lSql.= 'FROM (SELECT `parent_id`, max(`version`) AS maxver, `language` ';
      $lSql.= 'FROM `al_cms_ref_lang` WHERE `language`="'.$aLang.'" GROUP BY `parent_id`,`language`) as a ';
      $lSql.= 'INNER JOIN `al_cms_ref_lang` b ON (a.`parent_id`=b.`parent_id` AND a.`maxver`=b.`version` AND a.`language`=b.`language`) ';
      $lSql.= 'INNER JOIN (SELECT * FROM `al_cms_content` WHERE `mand`='.MID.') c ON (b.`content_id`=c.`content_id`) ';
      $lSql.= 'INNER JOIN `al_cms_ref_category` d ON (b.`content_id`=d.`content_id`) ';
      $lSql.= 'ORDER BY c.`parent_id`, c.`content` ASC';
	    $lQry = new CCor_Qry($lSql);
	    foreach ($lQry as $lRow) {
	      $lPid = $lRow['parent_id'];
	      $lCid = $lRow['content_id'];
	      $lVer = $lRow['ver'];
	      $lCont = strip_tags($lRow['content']);
	      $lTokens = explode(" ", $lRow['tokens']);
	      $lCategory = $lRow['category'];
	      $lTokenSize = sizeof($lTokens);
	      $lStatus = $lRow['status'];
	      
	      $lTokenWeight = 1 / $lTotal;
	      $lMaxPerc = ($lTotal < $lTokenSize) ? ($lTotal / $lTokenSize) : ($lTokenSize / $lTotal);

	      if ($lMaxPerc > 0) {
	    	$lTotalMatches = 0;
	    	
	    	foreach ($lTokens as $lToken) {
	    	  foreach ($aTokens as $lKey => $lVal){
	    	    if(strpos($lToken, $lVal) !== false){
	    	      $lPos = strpos($lToken, $lVal);
	    	      if($lPos == 0)
	    	        $lTotalMatches += 1;
	    	    }
			  }
	    	}
	    	
	    	$lMatchPerc = $lTotalMatches * $lTokenWeight;
	    	$lScore = $lMatchPerc * $lMaxPerc;
	    	
	    	if ($lScore > 0) {
	    	  $lSuggs[] = array(
	    	  	'content_id' => $lCid,
	    	  	'parent_id' => $lPid,
	    	  	'content' => $lCont,
	    	  	'tokens' => $lTokens,
	    	  	'language' => $aLang,
	    	  	'version' => $lVer,
	    	    'categories' => $lCategory,
	    	  	'status' => $lStatus,
	    	  	'matchScore' => $lScore
		      );
		    }
	      }
    	}
    	
    	return $lSuggs;
      } catch (Exception $e) {
    	return FALSE;
      }
    }
    
    /**
     * 
     * Function to sort a multidimensional array (NOT OURS).
     * @param array $aArr An array to sort
     * @param string $aOn What part of the array to sort on
     * @param string $aOrd The sorting order to use either DESC or ASC
     * @return $lRes The sorted array
     */
    public function sortArray($aArr, $aOn, $aOrd = "SORT_DESC") {
      $lRes = array();
      $lArr = $aArr;
      
      if (count($lArr) > 0) {
        $lSortArr = array();
    	foreach ($lArr as $lKey => $lVal) { //in array with each piece of content
    	  foreach ($lVal as $lKey2 => $lVal2) { //inside value to see if key equals what to order on
    		if ($lKey2 == $aOn){
    		  $lVal2 = (is_array($lVal2) ? array_map('strtolower', $lVal2[0]) : strtolower($lVal2));
    		  $lSortArr[$lKey] = $lVal2;
    		}
    	  }
    	}
    	
    	if ($aOrd == "SORT_ASC") {
    	  asort($lSortArr);
        } else  arsort($lSortArr);
    					
    	$lNewArr = array();
    	foreach($lSortArr as $lKey => $lVal){
    	  $lNewArr[] = $lArr[$lKey];
        }
    					
    	$lRes = $lNewArr;
      }


      return $lRes;
    }
    
    /**
    * Gets the relevant stopwords for the given language
    * @param String $aLang
    */
    protected function relevantStopwords($aLang){
    	$this -> prepareStopwords();
    	
    	return $this -> mSwords[$aLang];
    }
    
    /**
     * Initalises the stopwords array
     */
    protected function prepareStopwords(){
    	
        $this->mSwords["AR"] = array(); //Arabic
        
        $this->mSwords["BE"] = array(); //Belarusian
        
    	$this->mSwords["BG"] = array(); //Bulgarian
    	
    	$this->mSwords["ZH"] = array(); //Chinese
    	
    	$this->mSwords["HR"] = array(); //Croatian
    	
    	$this->mSwords["CS"] = array(); //Czech
    	
    	$this->mSwords["DA"] = array("af","alle","andet","andre","at","begge","da","de","den","denne","der","deres","det",
    					"dette","dig","din","dog","du","ej","eller","en","end","ene","eneste","enhver","et","etc","fem","fire","flere","fleste",
    					"for","fordi","forrige","fra","få","før","god","han","hans","har","hendes","her","hun","hvad","hvem","hver","hvilken",
    					"hvis","hvor","hvordan","hvorfor","hvornår","i","ikke","ind","ingen","intet","jeg","jeres","kan","kom","kommer",
    					"lav","lidt","lille","man","mand","mange","med","meget","men","mens","mere","mig","ned","ni","nogen","noget","ny",
    					"nyt","nær","næste","næsten","og","op","otte","over","på","se","seks","ses","som","stor","store","syv","ti","til",
    					"to","tre","ud","var"); //Danish
    
    	$this->mSwords["NL"] = array("aan","af","al","als","bij","dan","dat","die","dit","een","en","er","had","heb","hem","het",
    					"hij","hoe","hun","ik","in","is","je","kan","me","men","met","mij","nog","nu","of","ons","ook","te","tot",
    					"uit","van","was","wat","we","wel","wij","zal","ze","zei","zij","zo","zou"); //Dutch
    
    	$this->mSwords["EN"] = array("a","about","above","across","after","afterwards","again","against","all",
    					"almost","alone","along","already","also","although","always","am","amount","an","and","another", 
    					"any","anyhow","anyone","anything","anyway","anywhere","are","as","at","back","be","became","because",
    					"become","becomes","becoming","been","before","being","both","bottom","but","by","call","can", 
    					"cannot","con","could","describe","detail","do","done","due","during","each","eg","either", 
    					"else","elsewhere","empty","enough","etc","even","ever","every","everyone","everything","everywhere", 
    					"except","few","fill","find","for","former","found","from","front","full","further","get","give", 
    					"go","had","has","have","he","hence","here","how","however","if","in","into","is","it", 
    					"itself","last","least","less","made","many","may","meanwhile","might","more","most", 
    					"move","much","must","my","name","neither","never","next","no","nobody","none","not","nothing", 
    					"now","nowhere","of","off","often","on","once","only","onto","or","other","otherwise","our", 
    					"over","own","part","perhaps","put","rather","same","see","seem","she","should","since", 
    					"so","some","somehow","someone","something","sometime","somewhere","still","such","take","than", 
    					"that","the","their","them","then","there","these","they","thin","this","those","though", 
    					"through","thus","to","too","top","toward","under","until","up","upon","us","very","via","was", 
    					"we","well","were","what","whatever","when","whenever","where","wherever","whether","which", 
    					"while","who","whoever","whole","whom","whose","why","will","with","within","without","would", 
    					"yet","you","your","yours"); //English
    
    	$this->mSwords["ET"] = array(); //Estonian
    	
    	$this->mSwords["FL"] = array("aiemmin","aika","aikaa","aikaan","aikaisemmin","aikaisin","aikajen","aikana","aikoina",
    					"aikoo","aikovat","aina","ainakaan","ainakin","ainoa","ainoat","aiomme","aion","aiotte","aist","avian",
    					"ajan","älä","alas","alemmas","älköön","alkuisin","alkuun","alla","alle","aloitamme","aloitan","aloitat",
    					"aloitatte","aloitattivat","aloitettava","aloitettevaksi","aloitettu","aloitimme","aloitin","aloitit",
    					"aloititte","aloittaa","aloittamatta","aloitti","aloittivat","alta","aluksi","alussa","alusta","annettavaksi",
    					"annetteva","annettu","antaa","antamatta","antoi","aoua","apu","asia","asiaa","Asian","asiasta","asiat",
    					"asioiden","asioihin","asioita","asti","avuksi","avulla","avun","avutta","edellä","edelle","edelleen",
    					"edeltä","edemmäs","edes","edessä","edestä","ehkä","ei","eikä","eilen","eivät","eli","ellei","elleivät",
    					"ellemme","ellen","ellet","ellette","emme","en","enää","enemmän","eniten","ennen","ensi","ensimmäinen",
    					"ensimmäiseksi","ensimmäisen","ensimmäisenä","ensimmäiset","ensimmäisiä","ensimmäisiksi","ensimmäisinä",
    					"ensimmäistä","ensin","entinen","entisen","entisiä","entistä","entisten","eräät","eräiden","eras","eri",
    					"erittäin","erityisesti","esi","esiin","esillä","esimerkiksi","et","eteen","etenkin","että","ette","ettei",
    					"halua","haluaa","haluamatta","haluamme","haluan","haluat","haluatte","haluavat","halunnut","halusi",
    					"halusimme","halusin","halusit","halusitte","halusivat","halutessa","haluton","hän","häneen","hänellä",
    					"hänelle","häneltä","hänen","hänessä","hänestä","hänet","he","hei","heidän","heihin","heille","heiltä",
    					"heissä","heistä","heitä","helposti","heti","hetkellä","hieman","huolimatta","huomenna","hyvä","hyvää",
    					"hyvät","hyviä","hyvien","hyviin","hyviksi","hyville","hyviltä","hyvin","hyvinä","hyvissä","hyvistä",
    					"ihan","ilman","ilmeisesti","itse","itseään","itsensä","ja","jää","jälkeenjälleen","jo","johon","joiden",
    					"joihin","joiksi","joilla","joille","joilta","joissa","joista","joita","joka","jokainen","jokin","joko",
    					"joku","jolla","jolle","jolloin","jolta","jompikumpi","jonka","jonkin","jonne","joo","jopa","jos","joskus",
    					"jossa","josta","jota","jotain","joten","jotenkin","jotenkuten","jotka","jotta","jouduimme","jouduin",
    					"jouduit","jouduitte","joudumme","joudun","joudutte","joukkoon","joukossa","joukosta","joutua","joutui",
    					"joutuivat","joutumaan","joutuu","joutuvat","juuri","kahdeksan","kahdeksannen","kahdella","kahdelle",
    					"kahdelta","kahden","kahdessa","kahdesta","kahta","kahteen","kai","kaiken","kaikille","kaikilta","kaikkea",
    					"kaikki","kaikkia","kaikkiaan","kaikkialla","kaikkialle","kaikkialta","kaikkien","kaikkin","kaksi","kannalta",
    					"kannattaa","kanssa","kanssaan","kanssamme","kanssani","kanssanne","kanssasi","kauan","kauemmas","kautta",
    					"kehen","keiden","keihin","keiksi","keillä","keille","keiltä","keinä","keissä","keistä","keitä",
    					"keittäkeitten","keneen","keneksi","kenellä","kenelle","keneltä","kenen","kenenä","kenessä","kenestä",
    					"kenet","kenettä","kennessästä","kerran","kerta","kertaa","kesken","keskimäärin","ketä","ketkä","kiitos",
    					"kohti","koko","kokonaan","kolmas","kolme","kolmen","kolmesti","koska","koskaan","kovin","kuin","kuinka",
    					"kuitenkaan","kuitenkin","kuka","kukaan","kukin","kumpainen","kumpainenkaan","kumpi","kumpikaan","kumpikin",
    					"kun","kuten","kuuden","kuusi","kuutta","kyllä","kymmenen","kyse","lähekkäin","lähellä","lähelle","läheltä",
    					"lähemmäs","lähes","lähinnä","lähtien","läpi","liian","liki","lisää","lisäksi","luo","mahdollisimman",
    					"mahdollista","me","meidän","meillä","meille","melkein","melko","menee","meneet","menemme","menen","menet",
    					"menette","menevät","meni","menimme","menin","menit","menivät","mennessä","mennyt","menossa","mihin","mikä",
    					"mikään","mikäli","mikin","miksi","million","mina","mine","minun","minut","missä","mistä","mitä","mitään",
    					"miten","moi","molemmat","mones","monesti","monet","moni","moniaalla","moniaalle","moniaalta","monta",
    					"muassa","muiden","muita","muka","mukaan","mukaansa","mukana","mutta","muu","muualla","muualle","muualta",
    					"muuanne","muulloin","muun","muut","muuta","muutama","muutaman","muuten","myöhemmin","myös","myöskään",
    					"myöskin","myötä","näiden","näin","näissä","näissähin","näissälle","näissältä","näissästä","näitä","nämä",
    					"ne","neljä","neljää","neljän","niiden","niin","niistä","niitä","noin","nopeammin","nopeasti","nopeiten",
    					"nro","nuo","nyt","ohi","oikein","ole","olemme","olen","olet","olette","oleva","olevan","olevat","oli",
    					"olimme","olin","olisi","olisimme","olisin","olisit","olisitte","olisivat","olit","olitte","olivat","olla",
    					"olleet","olli","ollut","oma","omaa","omaan","omaksi","omalle","omalta","oman","omassa","omat","omia","omien",
    					"omiin","omiksi","omille","omilta","omissa","omista","on","onkin","onko","ovat","päälle","paikoittain",
    					"paitsi","pakosti","paljon","paremmin","parempi","parhaillaan","parhaiten","peräti","perusteella","pian",
    					"pieneen","pieneksi","pienellä","pienelle","pieneltä","pienempi","pienestä","pieni","pienin","puolesta",
    					"puolestaan","runsaasti","saakka","sadam","sama","samaa","samaan","samalla","samallalta","samallassa",
    					"samallasta","saman","samat","samoin","sata","sataa","satojen","se","seitsemän","sekä","sen","seuraavat",
    					"siellä","sieltä","siihen","siinä","siis","siitä","sijaan","siksi","sillä","silloin","silti","sinä","sinne",
    					"sinua","sinulle","sinulta","sinun","sinussa","sinusta","sinut","sisäkkäin","sisällä","sitä","siten","sitten",
    					"suoraan","suuntaan","suuren","suuret","suuri","suuria","suurin","suurten","taa","täällä","täältä","taas",
    					"taemmas","tähän","tahansa","tai","taka","takaisin","takana","takia","tällä","tällöin","tama","tämän","tänä",
    					"tänään","tänne","tapauksessa","tässä","tästä","tätä","täten","tavalla","tavoitteena","täysin","täytyvät",
    					"täytyy","te","tietysti","todella","toinen","toisaalla","toisaalle","toisaalta","toiseen","toiseksi",
    					"toisella","toiselle","toiselta","toisemme","toisen","toisensa","toisessa","toisesta","toista","toistaiseksi",
    					"toki","tosin","tuhannen","tuhat","tule","tulee","tulemme","tulen","tulet","tulette","tulevat","tulimme",
    					"tulin","tulisi","tulisimme","tulisin","tulisit","tulisitte","tulisivat","tulit","tulitte","tulivat","tulla",
    					"tulleet","tullut","tuntuu","tuo","tuolla","tuolloin","tuolta","tuonne","tuskin","tykö","usea","useasti",
    					"useimmiten","usein","useita","uudeksi","uudelleen","uuden","uudet","uusi","uusia","uusien","uusinta",
    					"uuteen","uutta","vaan","vähän","vähemmän","vähintään","vähiten","vai","vaiheessa","vaikea","vaikean",
    					"vaikeat","vaikeilla","vaikeille","vaikeilta","vaikeissa","vaikeista","vaikka","vain","välillä","varmasti",
    					"varsin","varsinkin","varten","vasta","vastaan","vastakkain","verran","vielä","vierekkäin","vieri","viiden",
    					"viime","viimeinen","viimeisen","viimeksi","viisi","voi","voidaan","voimme","voin","voisi","voit","voitte",
    					"voivat","vuoden","vuoksi","vuosi","vuosien","vuosina","vuotta","yhä","yhdeksän","yhden","yhdessä",
    					"yhtä","yhtäällä","yhtäälle","yhtäältä","yhtään","yhteen","yhteensä","yhteydessä","yhteyteen","yksi",
    					"yksin","yksittäin","yleensä","ylemmäs","yli","ylös","ympäri"); //Finnish
    
    	$this->mSwords["FR"] = array("alors","au","aucuns","aussi","autre","avant","avec","avoir","bon","car","ce","cela","ces",
    					"ceux","chaque","ci","comme","comment","dans","des","du","dedans","dehors","depuis","deux","devrait","doit",
    					"donc","dos","droite","début","elle","ells","en","encore","essai","est","et","eu","fait","faites","fois",
    					"font","force","haut","hors","ici","il","ils","je","juste","la","le","les","leur","là","ma","maintenant",
    					"mais","mes","mine","moins","mon","mot","meme","ni","nommés","notre","nous","nouveaux","ou","où","par",
    					"parce","parole","pas","personnes","peut","peu","pièce","plupart","pour","pourquoi","quand","que","quell",
    					"quelle","quelles","quells","qui","sa","sans","ses","seulement","si","sien","son","sont","sous","soyez",
    					"sujet","sur","ta","tandis","tellement","tells","tes","ton","tous","tout","trop","très","tu","valeur","voie",
    					"voient","vont","votre","vous","vu","ça","étaient","état","étions","été","être"); //french
    
    	$this->mSwords["DE"] = array("aber","als","am","an","auch","auf","aus","bei","bin","bis","bist","da","dadurch","daher",
    					"darum","das","daß","dass","dein","deine","dem","den","der","des","dessen","deshalb","die","dies","dieser",
    					"dieses","doch","dort","du","durch","ein","eine","einem","einen","einer","eines","er","es","euer","eure",
    					"für","hatte","hatten","hattest","hattet","hier","hinter","ich","ihr","ihre","im","in","ist","ja","jede",
    					"jedem","jeden","jeder","jedes","jener","jenes","jetzt","kann","kannst","können","könnt","machen","mein",
    					"meine","mit","muß","mußt","musst","müssen","müßt","nach","nachdem","nein","nicht","nun","oder","seid",
    					"sein","seine","sich","sie","sind","soll","sollen","sollst","sollt","sonst","soweit","sowie","und","unser",
    					"unsere","unter","vom","von","vor","wann","warum","was","weiter","weitere","wenn","wer","werde","werden",
    					"werdet","weshalb","wie","wieder","wieso","wir","wird","wirst","wo","woher","wohin","zu","zum","zur","über"); //German
		
    	$this->mSwords["EL"] = array(); //Greek
    	
    	$this->mSwords["HI"] = array(); //Hindi
    	
    	$this->mSwords["HU"] = array(); //Hungarian
    	
    	$this->mSwords["IS"] = array(); //Icelandic
    	
    	$this->mSwords["ID"] = array(); //Indonesian
    	
    	$this->mSwords["IT"] = array("a","adesso","ai","al","alla","allo","allora","altre","altri","altro","anche","ancora",
    					"avere","aveva","avevano","ben","buono","che","chi","cinque","comprare","con","consecutivi","consecutivo",
    					"cosa","cui","da","del","della","dello","dentro","deve","devo","di","doppio","due","e","ecco","fare","fine",
    					"fino","fra","gente","giu","ha","hai","hanno","ho","il","indietro","invece","io","la","lavoro","le","lei",
    					"lo","loro","lui","lungo","ma","me","meglio","molta","molti","molto","nei","nella","no","noi","nome",
    					"nostro","nove","nuovi","nuovo","o","oltre","ora","otto","peggio","pero","persone","piu","poco","primo",
    					"promesso","qua","quarto","quasi","quattro","quello","questo","qui","quindi","quinto","rispetto","sara",
    					"secondo","sei","sembra","sembrava","senza","sette","sia","siamo","siete","solo","sono","sopra",
    					"soprattutto","sotto","stati","stato","stesso","su","subito","sul","sulla","tanto","te","tempo","terzo",
    					"tra","tre","triplo","ultimo","un","una","uno","va","vai","voi","volte","vostro"); //Italian
    	
    	$this->mSwords["JA"] = array(); //Japanese
						 
    	$this->mSwords["KO"] = array(); //Korean
		
    	$this->mSwords["LA"] = array(); //Latin
		
    	$this->mSwords["LV"] = array(); //Latvian
    	   
    	$this->mSwords["LT"] = array(); //Lithuanian
    	
    	$this->mSwords["MK"] = array(); //Macedonian
    	
    	$this->mSwords["MS"] = array(); //Malay
    	
    	$this->mSwords['NO'] = array("alle","andre","arbeid","av","begge","bort","bra","bruke","da","denne","der","deres",
    					"det","din","disse","du","eller","en","ene","eneste","enhver","enn","er","et","folk","for","fordi",
    					"forsÛke","fra","fÅ","fÛr","fÛrst","gjorde","gjÛre","god","gÅ","ha","hadde","han","hans","hennes","her",
    					"hva","hvem","hver","hvilken","hvis","hvor","hvordan","hvorfor","i","ikke","inn","innen","kan","kunne",
    					"lage","lang","lik","like","makt","mange","med","meg","meget","men","mens","mer","mest","min","mye","mÅ",
    					"mÅte","navn","nei","ny","nÅ","nÅr","og","ogsÅ","om","opp","oss","over","part","punkt","pÅ","rett","riktig",
    					"samme","sant","si","siden","sist","skulle","slik","slutt","som","start","stille","sÅ","tid","til","tilbake",
    					"tilstand","under","ut","uten","var","ved","verdi","vi","vil","ville","vite","vÅr","vÖre","vÖrt","Å"); //Norwegian
    	
    	$this->mSwords["PA"] = array(); //Panjabi
    
    	$this->mSwords["PL"] = array(); //Polish
    	
    	$this->mSwords['PT'] = array("último","é","acerca","agora","algmas","alguns","ali","ambos","antes","apontar",
    					"aquela","aquelas","aquele","aqueles","aqui","atrás","bem","bom","cada","caminho","cima","com","como",
    					"comprido","conhecido","corrente","das","debaixo","dentro","desde","desligado","deve","devem","deverá",
    					"direita","diz","dizer","dois","dos","e","ela","ele","eles","em","enquanto","então","está","estão","estado",
    					"estar","estará","este","estes","esteve","estive","estivemos","estiveram","eu","fará","faz","fazer","fazia",
    					"fez","fim","foi","fora","horas","iniciar","inicio","ir","irá","ista","iste","isto","ligado","maioria",
    					"maiorias","mais","mas","mesmo","meu","muito","muitos","nós","não","nome","nosso","novo","o","onde","os",
    					"ou","outro","para","parte","pegar","pelo","pessoas","pode","poderá","podia","por","porque","povo",
    					"promeiro","quê","qual","qualquer","quando","quem","quieto","são","saber","sem","ser","seu","somente",
    					"têm","tal","também","tem","tempo","tenho","tentar","tentaram","tente","tentei","teu","teve","tipo","tive",
    					"todos","trabalhar","trabalho","tu","um","uma","umas","uns","usa","usar","valor","veja","ver","verdade",
    					"verdadeiro","você"); //Portuguese
    	
    	$this->mSwords["RO"] = array(); //Romanian
    	
    	$this->mSwords["RU"] = array(); //Russian
    	
    	$this->mSwords["SR"] = array(); //Serbian
    	
    	$this->mSwords["SK"] = array(); //Slovak
    	
    	$this->mSwords["SL"] = array(); //Slovenian
    
    	$this->mSwords["ES"] = array("un","una","unas","unos","uno","sobre","todo","también","tras","otro","algún","alguno",
    					"alguna","algunos","algunas","ser","es","soy","eres","somos","sois","estoy","esta","estamos","estais",
    					"estan","como","en","para","atras","porque","por qué","estado","estaba","ante","antes","siendo","ambos",
    					"pero","por","poder","puede","puedo","podemos","podeis","pueden","fui","fue","fuimos","fueron","hacer",
    					"hago","hace","hacemos","haceis","hacen","cada","fin","incluso","primero","desde","conseguir","consigo",
    					"consigue","consigues","conseguimos","consiguen","ir","voy","va","vamos","vais","van","vaya","gueno","ha",
    					"tener","tengo","tiene","tenemos","teneis","tienen","el","la","lo","las","los","su","aqui","mio","tuyo",
    					"ellos","ellas","nos","nosotros","vosotros","vosotras","si","dentro","solo","solamente","saber","sabes",
    					"sabe","sabemos","sabeis","saben","ultimo","largo","bastante","haces","muchos","aquellos","aquellas","sus",
    					"entonces","tiempo","verdad","verdadero","verdadera","cierto","ciertos","cierta","ciertas","intentar",
    					"intento","intenta","intentas","intentamos","intentais","intentan","dos","bajo","arriba","encima","usar",
    					"uso","usas","usa","usamos","usais","usan","emplear","empleo","empleas","emplean","ampleamos","empleais",
    					"valor","muy","era","eras","eramos","eran","modo","bien","cual","cuando","donde","mientras","quien","con",
    					"entre","sin","trabajo","trabajar","trabajas","trabaja","trabajamos","trabajais","trabajan","podria",
    					"podrias","podriamos","podrian","podriais","yo","aquel"); //Spanish
    
    	$this->mSwords["SV"] = array("aderton","adertonde","adjö","aldrig","alla","allas","allt","alltid","alltså","än",
    					"andra","andras","annan","annat","ännu","artonde","artonn","åtminstone","att","åtta","åttio","åttionde",
    					"åttonde","av","även","båda","bådas","bakom","bara","bäst","bättre","behöva","behövas","behövde","behövt",
    					"beslut","beslutat","beslutit","bland","blev","bli","blir","blivit","bort","borta","bra","då","dag","dagar",
    					"dagarna","dagen","där","därför","de","del","delen","dem","den","deras","dess","det","detta","dig","din",
    					"dina","dit","ditt","dock","du","efter","eftersom","elfte","eller","elva","en","enkel","enkelt","enkla",
    					"enligt","er","era","ert","ett","ettusen","få ","fanns","får","fått ","fem","femte","femtio","femtionde",
    					"femton","femtonde","fick","fin","finnas","finns","fjärde","fjorton","fjortonde","fler","flera","flesta",
    					"följande","för","före","förlåt","förra","första","fram","framför","från","fyra","fyrtio","fyrtionde","gå",
    					"gälla","gäller","gällt","går","gärna","gått","genast","genom","gick","gjorde","gjort","god","goda","godare",
    					"godast","gör","göra","gott","ha","hade","haft","han","hans","har","här","heller","hellre","helst","helt",
    					"henne","hennes","hit","hög","höger","högre","högst","hon","honom","hundra","hundraen","hundraett","hur",
    					"i","ibland","idag","igår","igen","imorgon","in","inför","inga","ingen","ingenting","inget","innan","inne",
    					"inom","inte","inuti","ja","jag","jämfört","kan","kanske","knappast","kom","komma","kommer","kommit","kr",
    					"kunde","kunna","kunnat","kvar","länge","längre","långsam","långsammare","långsammast","långsamt","längst",
    					"långt","lätt","lättare","lättast","legat","ligga","ligger","lika","likställd","likställda","lilla","lite",
    					"liten","litet","man","många","måste","med","mellan","men","mer","mera","mest","mig","min","mina","mindre",
    					"minst","mitt","mittemot","möjlig","möjligen","möjligt","möjligtvis","mot","mycket","någon","någonting",
    					"något","några","när","nästa","ned","nederst","nedersta","nedre","nej","ner","ni","nio","nionde","nittio",
    					"nittionde","nitton","nittonde","nödvändig","nödvändiga","nödvändigt","nödvändigtvis","nog","noll","nr",
    					"nu","nummer","och","också","ofta","oftast","olika","olikt","om","oss","över","övermorgon","överst","övre",
    					"på","rakt","rätt","redan","så","sade","säga","säger","sagt","samma","sämre","sämst","sedan","senare",
    					"senast","sent","sex","sextio","sextionde","sexton","sextonde","sig","sin","sina","sist","sista","siste",
    					"sitt","sjätte","sju","sjunde","sjuttio","sjuttionde","sjutton","sjuttonde","ska","skall","skulle",
    					"slutligen","små","smått","snart","som","stor","stora","större","störst","stort","tack","tidig","tidigare",
    					"tidigast","tidigt","till","tills","tillsammans","tio","tionde","tjugo","tjugoen","tjugoett","tjugonde",
    					"tjugotre","tjugotvå","tjungo","tolfte","tolv","tre","tredje","trettio","trettionde","tretton","trettonde",
    					"två","tvåhundra","under","upp","ur","ursäkt","ut","utan","utanför","ute","vad","vänster","vänstra","var",
    					"vår","vara","våra","varför","varifrån","varit","varken","värre","varsågod","vart","vårt","vem","vems",
    					"verkligen","vi","vid","vidare","viktig","viktigare","viktigast","viktigt","vilka","vilken","vilket","vill"); //Swedish
		
    	$this->mSwords["TR"] = array(); //Turkish
		
    	$this->mSwords["UK"] = array(); //Ukrainian
    	
    	$this->mSwords["VI"] = array(); //Vietnamese
    }
}