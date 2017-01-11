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
	
	public static $mSwords;
	
	/**
	 * Function used to clean a string
	 * @param string $aComment A comment entered by the user
	 * @param string $aLang A comment entered by the user
	 * @param string $aTyp Optional parameter for returning a string instead of matching tokens
	 * @return Array<string, int> If no type supplied individual token and its match rating
	 * @return Array<string, int> If type supplied a string and the size of the string
	 */
	public static function sanitise($aComment, $aCat, $aLang, $aTyp = FALSE) {
  	  $lLang = ($aLang == '') ? CCor_Cfg::get("masterlanguage", "EN") : $aLang;
  	  
  	  self::$mSwords = self::relevantStopwords($lLang);
  	  $lStopTokens = $lSanTokens = array();
  	  $lComment = trim(strip_tags($aComment));
  	
  	  $lTokens = (strpos($lComment, " ") > -1 ? explode(" ", $lComment) : array($lComment));
  	  foreach ($lTokens as $lToken) {
  		$lRes = self::removeStops($lToken);
  		if ($lRes != "")
  			$lStopTokens[] = $lRes;
  	  }
  	
  	  $lStopTokens = array_unique($lStopTokens); 
  	  foreach ($lStopTokens as $lStop) {
  		$lSanTokens[] = self::stemToken($lStop, $lLang);
  	  }
  	
  	  if ($aTyp) {
  		return implode(" ", $lSanTokens);
  	  } else {
  		$lSuggs = self::weightedScore($lSanTokens, $aCat, $lLang);
  		
  		return self::sortArray($lSuggs, 'matchScore');
  	  }
	}

    /**
    * Function to convert a token to lowercase, remove whitespace, remove punctuation and check to see if it is 
    * in a stop list.
    * @param string $aToken token to clean.
    * @return A cleaned token or blank 
    */
    protected static function removeStops($aToken) {
  	  $lToken = trim(strtolower($aToken));
  	  $lToken = preg_replace("#[[:punct:]]#", "", $lToken);
  	
  	  return (in_array($lToken, self::$mSwords)) ? "" : $lToken;
    }
    
    /**
     * Function to stem any given token. E.g. Running is stemmed to run.
     * @param string $aToken token to stem.
     * @param string $aLang string language.
     * @return The stemmed token
     */
    protected static function stemToken($aToken, $aLang) {
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
    protected static function weightedScore($aTokens, $aCat, $aLang) {
      try {
	    $lSuggs = array();
	    $lTotal = sizeof($aTokens);
	    if ($lTotal == 0) return FALSE;
	    
	    $lSql = 'SELECT a.`content_id`, a.`parent_id`, b.`content`, b.`tokens`, b.`format`, b.`status`, c.`category`, a.`language`, a.`maxver` as ver ';
	    $lSql.= 'FROM (SELECT `content_id`, `parent_id`, max(`version`) AS maxver, `language` ';
	    $lSql.= 'FROM `al_cms_ref_lang` WHERE `language`='.esc($aLang).' GROUP BY `parent_id`,`language`) as a ';
	    //$lSql.= 'INNER JOIN `al_cms_ref_lang` b ON (a.`parent_id`=b.`parent_id` AND a.`maxver`=b.`version` AND a.`language`=b.`language`) ';
	    $lSql.= 'INNER JOIN (SELECT * FROM `al_cms_content` WHERE `mand`='.intval(MID).') as b ON (a.`content_id`=b.`content_id`) ';
	    $lSql.= 'INNER JOIN `al_cms_ref_category` as c ON (a.`content_id`=c.`content_id`) ';
	    if(!empty($aCat)) {
	      $lCat = explode('_', $aCat);
	      $lCat = $lCat[0];
	      $lSql.= 'WHERE c.`category`='.esc($lCat).' ';
	    }
	    $lSql.= 'ORDER BY b.`parent_id`, b.`content` ASC';
	    $lQry = new CCor_Qry($lSql);
	    foreach ($lQry as $lRow) {
	      $lCid = $lRow['content_id'];
	      $lTokens = explode(" ", $lRow['tokens']);
	      $lTokenSize = sizeof($lTokens);
	      
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
	    	  	'parent_id' => $lRow['parent_id'],
	    	  	'content' => strip_tags($lRow['content']),
	    	  	'tokens' => $lTokens,
	    	  	'format' => $lRow['format'],
	    	  	'language' => $lRow['language'],
	    	    'version' => $lRow['ver'],
	    	    'categories' => $lRow['category'],
	    	  	'metadata' => CCms_Mod::getMetadata($lCid),
	    	  	'jobs' => CCms_Mod::getJobs($lCid),
	    	  	'status' => $lRow['status'],
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
    protected static function relevantStopwords($aLang){
	    $lLang = ($aLang == 'MA') ? CCor_Cfg::get("masterlanguage", "EN") : $aLang;
	    
    	self::prepareStopwords();
    	
    	return self::$mSwords[$lLang];
    }
    
    /**
     * Initalises the stopwords array
     */
    protected static function prepareStopwords(){
        self::$mSwords["AR"] = array(); //Arabic
        
        self::$mSwords["BE"] = array(); //Belarusian
        
    	self::$mSwords["BG"] = array(); //Bulgarian
    	
    	self::$mSwords["ZH"] = array(); //Chinese
    	
    	self::$mSwords["HR"] = array(); //Croatian
    	
    	self::$mSwords["CS"] = array(); //Czech
    	
    	self::$mSwords["DA"] = array("af","alle","andet","andre","at","begge","da","de","den","denne","der","deres","det",
    					"dette","dig","din","dog","du","ej","eller","en","end","ene","eneste","enhver","et","etc","fem","fire","flere","fleste",
    					"for","fordi","forrige","fra","f�","f�r","god","han","hans","har","hendes","her","hun","hvad","hvem","hver","hvilken",
    					"hvis","hvor","hvordan","hvorfor","hvorn�r","i","ikke","ind","ingen","intet","jeg","jeres","kan","kom","kommer",
    					"lav","lidt","lille","man","mand","mange","med","meget","men","mens","mere","mig","ned","ni","nogen","noget","ny",
    					"nyt","n�r","n�ste","n�sten","og","op","otte","over","p�","se","seks","ses","som","stor","store","syv","ti","til",
    					"to","tre","ud","var"); //Danish
    
    	self::$mSwords["NL"] = array("aan","af","al","als","bij","dan","dat","die","dit","een","en","er","had","heb","hem","het",
    					"hij","hoe","hun","ik","in","is","je","kan","me","men","met","mij","nog","nu","of","ons","ook","te","tot",
    					"uit","van","was","wat","we","wel","wij","zal","ze","zei","zij","zo","zou"); //Dutch
    
    	self::$mSwords["EN"] = array("a","about","above","across","after","afterwards","again","against","all",
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
    
    	self::$mSwords["ET"] = array(); //Estonian
    	
    	self::$mSwords["FL"] = array("aiemmin","aika","aikaa","aikaan","aikaisemmin","aikaisin","aikajen","aikana","aikoina",
    					"aikoo","aikovat","aina","ainakaan","ainakin","ainoa","ainoat","aiomme","aion","aiotte","aist","avian",
    					"ajan","�l�","alas","alemmas","�lk��n","alkuisin","alkuun","alla","alle","aloitamme","aloitan","aloitat",
    					"aloitatte","aloitattivat","aloitettava","aloitettevaksi","aloitettu","aloitimme","aloitin","aloitit",
    					"aloititte","aloittaa","aloittamatta","aloitti","aloittivat","alta","aluksi","alussa","alusta","annettavaksi",
    					"annetteva","annettu","antaa","antamatta","antoi","aoua","apu","asia","asiaa","Asian","asiasta","asiat",
    					"asioiden","asioihin","asioita","asti","avuksi","avulla","avun","avutta","edell�","edelle","edelleen",
    					"edelt�","edemm�s","edes","edess�","edest�","ehk�","ei","eik�","eilen","eiv�t","eli","ellei","elleiv�t",
    					"ellemme","ellen","ellet","ellette","emme","en","en��","enemm�n","eniten","ennen","ensi","ensimm�inen",
    					"ensimm�iseksi","ensimm�isen","ensimm�isen�","ensimm�iset","ensimm�isi�","ensimm�isiksi","ensimm�isin�",
    					"ensimm�ist�","ensin","entinen","entisen","entisi�","entist�","entisten","er��t","er�iden","eras","eri",
    					"eritt�in","erityisesti","esi","esiin","esill�","esimerkiksi","et","eteen","etenkin","ett�","ette","ettei",
    					"halua","haluaa","haluamatta","haluamme","haluan","haluat","haluatte","haluavat","halunnut","halusi",
    					"halusimme","halusin","halusit","halusitte","halusivat","halutessa","haluton","h�n","h�neen","h�nell�",
    					"h�nelle","h�nelt�","h�nen","h�ness�","h�nest�","h�net","he","hei","heid�n","heihin","heille","heilt�",
    					"heiss�","heist�","heit�","helposti","heti","hetkell�","hieman","huolimatta","huomenna","hyv�","hyv��",
    					"hyv�t","hyvi�","hyvien","hyviin","hyviksi","hyville","hyvilt�","hyvin","hyvin�","hyviss�","hyvist�",
    					"ihan","ilman","ilmeisesti","itse","itse��n","itsens�","ja","j��","j�lkeenj�lleen","jo","johon","joiden",
    					"joihin","joiksi","joilla","joille","joilta","joissa","joista","joita","joka","jokainen","jokin","joko",
    					"joku","jolla","jolle","jolloin","jolta","jompikumpi","jonka","jonkin","jonne","joo","jopa","jos","joskus",
    					"jossa","josta","jota","jotain","joten","jotenkin","jotenkuten","jotka","jotta","jouduimme","jouduin",
    					"jouduit","jouduitte","joudumme","joudun","joudutte","joukkoon","joukossa","joukosta","joutua","joutui",
    					"joutuivat","joutumaan","joutuu","joutuvat","juuri","kahdeksan","kahdeksannen","kahdella","kahdelle",
    					"kahdelta","kahden","kahdessa","kahdesta","kahta","kahteen","kai","kaiken","kaikille","kaikilta","kaikkea",
    					"kaikki","kaikkia","kaikkiaan","kaikkialla","kaikkialle","kaikkialta","kaikkien","kaikkin","kaksi","kannalta",
    					"kannattaa","kanssa","kanssaan","kanssamme","kanssani","kanssanne","kanssasi","kauan","kauemmas","kautta",
    					"kehen","keiden","keihin","keiksi","keill�","keille","keilt�","kein�","keiss�","keist�","keit�",
    					"keitt�keitten","keneen","keneksi","kenell�","kenelle","kenelt�","kenen","kenen�","keness�","kenest�",
    					"kenet","kenett�","kenness�st�","kerran","kerta","kertaa","kesken","keskim��rin","ket�","ketk�","kiitos",
    					"kohti","koko","kokonaan","kolmas","kolme","kolmen","kolmesti","koska","koskaan","kovin","kuin","kuinka",
    					"kuitenkaan","kuitenkin","kuka","kukaan","kukin","kumpainen","kumpainenkaan","kumpi","kumpikaan","kumpikin",
    					"kun","kuten","kuuden","kuusi","kuutta","kyll�","kymmenen","kyse","l�hekk�in","l�hell�","l�helle","l�helt�",
    					"l�hemm�s","l�hes","l�hinn�","l�htien","l�pi","liian","liki","lis��","lis�ksi","luo","mahdollisimman",
    					"mahdollista","me","meid�n","meill�","meille","melkein","melko","menee","meneet","menemme","menen","menet",
    					"menette","menev�t","meni","menimme","menin","menit","meniv�t","menness�","mennyt","menossa","mihin","mik�",
    					"mik��n","mik�li","mikin","miksi","million","mina","mine","minun","minut","miss�","mist�","mit�","mit��n",
    					"miten","moi","molemmat","mones","monesti","monet","moni","moniaalla","moniaalle","moniaalta","monta",
    					"muassa","muiden","muita","muka","mukaan","mukaansa","mukana","mutta","muu","muualla","muualle","muualta",
    					"muuanne","muulloin","muun","muut","muuta","muutama","muutaman","muuten","my�hemmin","my�s","my�sk��n",
    					"my�skin","my�t�","n�iden","n�in","n�iss�","n�iss�hin","n�iss�lle","n�iss�lt�","n�iss�st�","n�it�","n�m�",
    					"ne","nelj�","nelj��","nelj�n","niiden","niin","niist�","niit�","noin","nopeammin","nopeasti","nopeiten",
    					"nro","nuo","nyt","ohi","oikein","ole","olemme","olen","olet","olette","oleva","olevan","olevat","oli",
    					"olimme","olin","olisi","olisimme","olisin","olisit","olisitte","olisivat","olit","olitte","olivat","olla",
    					"olleet","olli","ollut","oma","omaa","omaan","omaksi","omalle","omalta","oman","omassa","omat","omia","omien",
    					"omiin","omiksi","omille","omilta","omissa","omista","on","onkin","onko","ovat","p��lle","paikoittain",
    					"paitsi","pakosti","paljon","paremmin","parempi","parhaillaan","parhaiten","per�ti","perusteella","pian",
    					"pieneen","pieneksi","pienell�","pienelle","pienelt�","pienempi","pienest�","pieni","pienin","puolesta",
    					"puolestaan","runsaasti","saakka","sadam","sama","samaa","samaan","samalla","samallalta","samallassa",
    					"samallasta","saman","samat","samoin","sata","sataa","satojen","se","seitsem�n","sek�","sen","seuraavat",
    					"siell�","sielt�","siihen","siin�","siis","siit�","sijaan","siksi","sill�","silloin","silti","sin�","sinne",
    					"sinua","sinulle","sinulta","sinun","sinussa","sinusta","sinut","sis�kk�in","sis�ll�","sit�","siten","sitten",
    					"suoraan","suuntaan","suuren","suuret","suuri","suuria","suurin","suurten","taa","t��ll�","t��lt�","taas",
    					"taemmas","t�h�n","tahansa","tai","taka","takaisin","takana","takia","t�ll�","t�ll�in","tama","t�m�n","t�n�",
    					"t�n��n","t�nne","tapauksessa","t�ss�","t�st�","t�t�","t�ten","tavalla","tavoitteena","t�ysin","t�ytyv�t",
    					"t�ytyy","te","tietysti","todella","toinen","toisaalla","toisaalle","toisaalta","toiseen","toiseksi",
    					"toisella","toiselle","toiselta","toisemme","toisen","toisensa","toisessa","toisesta","toista","toistaiseksi",
    					"toki","tosin","tuhannen","tuhat","tule","tulee","tulemme","tulen","tulet","tulette","tulevat","tulimme",
    					"tulin","tulisi","tulisimme","tulisin","tulisit","tulisitte","tulisivat","tulit","tulitte","tulivat","tulla",
    					"tulleet","tullut","tuntuu","tuo","tuolla","tuolloin","tuolta","tuonne","tuskin","tyk�","usea","useasti",
    					"useimmiten","usein","useita","uudeksi","uudelleen","uuden","uudet","uusi","uusia","uusien","uusinta",
    					"uuteen","uutta","vaan","v�h�n","v�hemm�n","v�hint��n","v�hiten","vai","vaiheessa","vaikea","vaikean",
    					"vaikeat","vaikeilla","vaikeille","vaikeilta","vaikeissa","vaikeista","vaikka","vain","v�lill�","varmasti",
    					"varsin","varsinkin","varten","vasta","vastaan","vastakkain","verran","viel�","vierekk�in","vieri","viiden",
    					"viime","viimeinen","viimeisen","viimeksi","viisi","voi","voidaan","voimme","voin","voisi","voit","voitte",
    					"voivat","vuoden","vuoksi","vuosi","vuosien","vuosina","vuotta","yh�","yhdeks�n","yhden","yhdess�",
    					"yht�","yht��ll�","yht��lle","yht��lt�","yht��n","yhteen","yhteens�","yhteydess�","yhteyteen","yksi",
    					"yksin","yksitt�in","yleens�","ylemm�s","yli","yl�s","ymp�ri"); //Finnish
    
    	self::$mSwords["FR"] = array("alors","au","aucuns","aussi","autre","avant","avec","avoir","bon","car","ce","cela","ces",
    					"ceux","chaque","ci","comme","comment","dans","des","du","dedans","dehors","depuis","deux","devrait","doit",
    					"donc","dos","droite","d�but","elle","ells","en","encore","essai","est","et","eu","fait","faites","fois",
    					"font","force","haut","hors","ici","il","ils","je","juste","la","le","les","leur","l�","ma","maintenant",
    					"mais","mes","mine","moins","mon","mot","meme","ni","nomm�s","notre","nous","nouveaux","ou","o�","par",
    					"parce","parole","pas","personnes","peut","peu","pi�ce","plupart","pour","pourquoi","quand","que","quell",
    					"quelle","quelles","quells","qui","sa","sans","ses","seulement","si","sien","son","sont","sous","soyez",
    					"sujet","sur","ta","tandis","tellement","tells","tes","ton","tous","tout","trop","tr�s","tu","valeur","voie",
    					"voient","vont","votre","vous","vu","�a","�taient","�tat","�tions","�t�","�tre"); //french
    
    	self::$mSwords["DE"] = array("aber","als","am","an","auch","auf","aus","bei","bin","bis","bist","da","dadurch","daher",
    					"darum","das","da�","dass","dein","deine","dem","den","der","des","dessen","deshalb","die","dies","dieser",
    					"dieses","doch","dort","du","durch","ein","eine","einem","einen","einer","eines","er","es","euer","eure",
    					"f�r","hatte","hatten","hattest","hattet","hier","hinter","ich","ihr","ihre","im","in","ist","ja","jede",
    					"jedem","jeden","jeder","jedes","jener","jenes","jetzt","kann","kannst","k�nnen","k�nnt","machen","mein",
    					"meine","mit","mu�","mu�t","musst","m�ssen","m��t","nach","nachdem","nein","nicht","nun","oder","seid",
    					"sein","seine","sich","sie","sind","soll","sollen","sollst","sollt","sonst","soweit","sowie","und","unser",
    					"unsere","unter","vom","von","vor","wann","warum","was","weiter","weitere","wenn","wer","werde","werden",
    					"werdet","weshalb","wie","wieder","wieso","wir","wird","wirst","wo","woher","wohin","zu","zum","zur","�ber"); //German
		
    	self::$mSwords["EL"] = array(); //Greek
    	
    	self::$mSwords["HI"] = array(); //Hindi
    	
    	self::$mSwords["HU"] = array(); //Hungarian
    	
    	self::$mSwords["IS"] = array(); //Icelandic
    	
    	self::$mSwords["ID"] = array(); //Indonesian
    	
    	self::$mSwords["IT"] = array("a","adesso","ai","al","alla","allo","allora","altre","altri","altro","anche","ancora",
    					"avere","aveva","avevano","ben","buono","che","chi","cinque","comprare","con","consecutivi","consecutivo",
    					"cosa","cui","da","del","della","dello","dentro","deve","devo","di","doppio","due","e","ecco","fare","fine",
    					"fino","fra","gente","giu","ha","hai","hanno","ho","il","indietro","invece","io","la","lavoro","le","lei",
    					"lo","loro","lui","lungo","ma","me","meglio","molta","molti","molto","nei","nella","no","noi","nome",
    					"nostro","nove","nuovi","nuovo","o","oltre","ora","otto","peggio","pero","persone","piu","poco","primo",
    					"promesso","qua","quarto","quasi","quattro","quello","questo","qui","quindi","quinto","rispetto","sara",
    					"secondo","sei","sembra","sembrava","senza","sette","sia","siamo","siete","solo","sono","sopra",
    					"soprattutto","sotto","stati","stato","stesso","su","subito","sul","sulla","tanto","te","tempo","terzo",
    					"tra","tre","triplo","ultimo","un","una","uno","va","vai","voi","volte","vostro"); //Italian
    	
    	self::$mSwords["JA"] = array(); //Japanese
						 
    	self::$mSwords["KO"] = array(); //Korean
		
    	self::$mSwords["LA"] = array(); //Latin
		
    	self::$mSwords["LV"] = array(); //Latvian
    	   
    	self::$mSwords["LT"] = array(); //Lithuanian
    	
    	self::$mSwords["MK"] = array(); //Macedonian
    	
    	self::$mSwords["MS"] = array(); //Malay
    	
    	self::$mSwords['NO'] = array("alle","andre","arbeid","av","begge","bort","bra","bruke","da","denne","der","deres",
    					"det","din","disse","du","eller","en","ene","eneste","enhver","enn","er","et","folk","for","fordi",
    					"fors�ke","fra","f�","f�r","f�rst","gjorde","gj�re","god","g�","ha","hadde","han","hans","hennes","her",
    					"hva","hvem","hver","hvilken","hvis","hvor","hvordan","hvorfor","i","ikke","inn","innen","kan","kunne",
    					"lage","lang","lik","like","makt","mange","med","meg","meget","men","mens","mer","mest","min","mye","m�",
    					"m�te","navn","nei","ny","n�","n�r","og","ogs�","om","opp","oss","over","part","punkt","p�","rett","riktig",
    					"samme","sant","si","siden","sist","skulle","slik","slutt","som","start","stille","s�","tid","til","tilbake",
    					"tilstand","under","ut","uten","var","ved","verdi","vi","vil","ville","vite","v�r","v�re","v�rt","�"); //Norwegian
    	
    	self::$mSwords["PA"] = array(); //Panjabi
    
    	self::$mSwords["PL"] = array(); //Polish
    	
    	self::$mSwords['PT'] = array("�ltimo","�","acerca","agora","algmas","alguns","ali","ambos","antes","apontar",
    					"aquela","aquelas","aquele","aqueles","aqui","atr�s","bem","bom","cada","caminho","cima","com","como",
    					"comprido","conhecido","corrente","das","debaixo","dentro","desde","desligado","deve","devem","dever�",
    					"direita","diz","dizer","dois","dos","e","ela","ele","eles","em","enquanto","ent�o","est�","est�o","estado",
    					"estar","estar�","este","estes","esteve","estive","estivemos","estiveram","eu","far�","faz","fazer","fazia",
    					"fez","fim","foi","fora","horas","iniciar","inicio","ir","ir�","ista","iste","isto","ligado","maioria",
    					"maiorias","mais","mas","mesmo","meu","muito","muitos","n�s","n�o","nome","nosso","novo","o","onde","os",
    					"ou","outro","para","parte","pegar","pelo","pessoas","pode","poder�","podia","por","porque","povo",
    					"promeiro","qu�","qual","qualquer","quando","quem","quieto","s�o","saber","sem","ser","seu","somente",
    					"t�m","tal","tamb�m","tem","tempo","tenho","tentar","tentaram","tente","tentei","teu","teve","tipo","tive",
    					"todos","trabalhar","trabalho","tu","um","uma","umas","uns","usa","usar","valor","veja","ver","verdade",
    					"verdadeiro","voc�"); //Portuguese
    	
    	self::$mSwords["RO"] = array(); //Romanian
    	
    	self::$mSwords["RU"] = array(); //Russian
    	
    	self::$mSwords["SR"] = array(); //Serbian
    	
    	self::$mSwords["SK"] = array(); //Slovak
    	
    	self::$mSwords["SL"] = array(); //Slovenian
    
    	self::$mSwords["ES"] = array("un","una","unas","unos","uno","sobre","todo","tambi�n","tras","otro","alg�n","alguno",
    					"alguna","algunos","algunas","ser","es","soy","eres","somos","sois","estoy","esta","estamos","estais",
    					"estan","como","en","para","atras","porque","por qu�","estado","estaba","ante","antes","siendo","ambos",
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
    
    	self::$mSwords["SV"] = array("aderton","adertonde","adj�","aldrig","alla","allas","allt","alltid","allts�","�n",
    					"andra","andras","annan","annat","�nnu","artonde","artonn","�tminstone","att","�tta","�ttio","�ttionde",
    					"�ttonde","av","�ven","b�da","b�das","bakom","bara","b�st","b�ttre","beh�va","beh�vas","beh�vde","beh�vt",
    					"beslut","beslutat","beslutit","bland","blev","bli","blir","blivit","bort","borta","bra","d�","dag","dagar",
    					"dagarna","dagen","d�r","d�rf�r","de","del","delen","dem","den","deras","dess","det","detta","dig","din",
    					"dina","dit","ditt","dock","du","efter","eftersom","elfte","eller","elva","en","enkel","enkelt","enkla",
    					"enligt","er","era","ert","ett","ettusen","f� ","fanns","f�r","f�tt ","fem","femte","femtio","femtionde",
    					"femton","femtonde","fick","fin","finnas","finns","fj�rde","fjorton","fjortonde","fler","flera","flesta",
    					"f�ljande","f�r","f�re","f�rl�t","f�rra","f�rsta","fram","framf�r","fr�n","fyra","fyrtio","fyrtionde","g�",
    					"g�lla","g�ller","g�llt","g�r","g�rna","g�tt","genast","genom","gick","gjorde","gjort","god","goda","godare",
    					"godast","g�r","g�ra","gott","ha","hade","haft","han","hans","har","h�r","heller","hellre","helst","helt",
    					"henne","hennes","hit","h�g","h�ger","h�gre","h�gst","hon","honom","hundra","hundraen","hundraett","hur",
    					"i","ibland","idag","ig�r","igen","imorgon","in","inf�r","inga","ingen","ingenting","inget","innan","inne",
    					"inom","inte","inuti","ja","jag","j�mf�rt","kan","kanske","knappast","kom","komma","kommer","kommit","kr",
    					"kunde","kunna","kunnat","kvar","l�nge","l�ngre","l�ngsam","l�ngsammare","l�ngsammast","l�ngsamt","l�ngst",
    					"l�ngt","l�tt","l�ttare","l�ttast","legat","ligga","ligger","lika","likst�lld","likst�llda","lilla","lite",
    					"liten","litet","man","m�nga","m�ste","med","mellan","men","mer","mera","mest","mig","min","mina","mindre",
    					"minst","mitt","mittemot","m�jlig","m�jligen","m�jligt","m�jligtvis","mot","mycket","n�gon","n�gonting",
    					"n�got","n�gra","n�r","n�sta","ned","nederst","nedersta","nedre","nej","ner","ni","nio","nionde","nittio",
    					"nittionde","nitton","nittonde","n�dv�ndig","n�dv�ndiga","n�dv�ndigt","n�dv�ndigtvis","nog","noll","nr",
    					"nu","nummer","och","ocks�","ofta","oftast","olika","olikt","om","oss","�ver","�vermorgon","�verst","�vre",
    					"p�","rakt","r�tt","redan","s�","sade","s�ga","s�ger","sagt","samma","s�mre","s�mst","sedan","senare",
    					"senast","sent","sex","sextio","sextionde","sexton","sextonde","sig","sin","sina","sist","sista","siste",
    					"sitt","sj�tte","sju","sjunde","sjuttio","sjuttionde","sjutton","sjuttonde","ska","skall","skulle",
    					"slutligen","sm�","sm�tt","snart","som","stor","stora","st�rre","st�rst","stort","tack","tidig","tidigare",
    					"tidigast","tidigt","till","tills","tillsammans","tio","tionde","tjugo","tjugoen","tjugoett","tjugonde",
    					"tjugotre","tjugotv�","tjungo","tolfte","tolv","tre","tredje","trettio","trettionde","tretton","trettonde",
    					"tv�","tv�hundra","under","upp","ur","urs�kt","ut","utan","utanf�r","ute","vad","v�nster","v�nstra","var",
    					"v�r","vara","v�ra","varf�r","varifr�n","varit","varken","v�rre","vars�god","vart","v�rt","vem","vems",
    					"verkligen","vi","vid","vidare","viktig","viktigare","viktigast","viktigt","vilka","vilken","vilket","vill"); //Swedish
		
    	self::$mSwords["TR"] = array(); //Turkish
		
    	self::$mSwords["UK"] = array(); //Ukrainian
    	
    	self::$mSwords["VI"] = array(); //Vietnamese
    }
}