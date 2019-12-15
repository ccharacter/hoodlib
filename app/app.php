<?php
session_start();


// CUSTOM FUNCTIONS

function sws_rm_brackets($text="Test [cowabunga if I may] this and that.", $stripTags="Y") {
	$ret=preg_replace('`\[[^\]]*\]`',"",$text);
	$ret=str_replace(" :",":",$ret);
	//error_log("BRACKET FUNCTION",0);
	//if (!(strpos($ret,"[")===false)) { 	error_log($ret,0); }
	if ($stripTags=="Y") { $ret=strip_tags($ret); }
	return $ret;
}

function sws_author($result) {

	$ret="";
    $authorList = [];
    $dump = 0;

    if ((isset($result['Items']['Au'])) && (is_array($result['Items']['Au']))) {
		foreach($result['Items']['Au'] as $Author){
			//first try a regular expression to find well formed occurences of <a href="...">...</a>
			$regexp = "<a\shref=\"[^\"]*\">.*<\/a>";
			preg_match_all("/$regexp/siU", $Author['Data'], $matches);
			$countMatches = count($matches[0]);

			//matchCount > 0 then let's use that array
			if($countMatches > 0){
				foreach($matches[0] as $author){
					$authorList[] =  $author;
				}
			}
			// if not test for occurence of a semicolon and explode from there
			elseif(strpos($Author['Data'], ';') > -1){
				$authors = explode(';', $Author['Data']);
				foreach($authors as $author){
					$authorList[] =  $author;
				}
			}
			// if this fails, just use the data as is (e.g. for a single Author)
			else{
				$authorList[] = $Author['Data'];
				$dump = 1;
			}                               
			
		} 
	}
	
	if (count($authorList)>0) { 
		$tmp=explode(",",$authorList[0]);  $ret=$tmp[0];
		if (count($authorList)>1) { $ret.=" et al";}
		return strip_tags($ret); 
	} else { return false; }
	
}

function sws_pubyear($result) {
	if(isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['date'])){
		foreach($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['date'] as $date){ 
			if (($date["Type"]=='published') && (strlen($date['Y'])>0)) {
				return "(".$date['Y'].") ";
			}
		}	
	}
	return false;
}

function sws_recordlink($result,$encodedHighLigtTerm,$encodedSearchTerm,$fieldCode) {
	$link="record.php?db=".$result['DbId']."&an=".$result['An']."&".$encodedHighLigtTerm."&resultId=".$result['ResultId'];
	if (isset($result['recordCount'])) { 
		$link.="&recordCount=".$result['recordCount']; 
	}
	$link.="&".$encodedSearchTerm."&fieldcode=".$fieldCode; 
	return $link;
}

function sws_permalink($result) {
	$myAN=sws_get_an($result); 
	if ($myAN) {
		$link="https://search.ebscohost.com/login.aspx?direct=true&db=cat00831a&AN=".$myAN."&site=eds-live";
		return $link;
	}
	return false;
}

// RETURNS AN ARRAY
function sws_get_isbn($result) {
	$retArr=array();
	if ( (isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Identifiers'])) && (is_array($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Identifiers'] )) ) {
		foreach ($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Identifiers'] as $arr) {
			//error_log(print_r($arr,true),0);
			$retArr[]=$arr['Value'];
		}
	}
	if (count($retArr)==0) { return false; } else { return $retArr; }
}

function sws_get_an($result) {
	if (isset($result['An'])) { return $result['An'];}
	return false;
}

function sws_trim_string($string,$max=50) {
	$ret=$string;
	if (strlen($string)>$max) {
		$ret=substr($string,0,$max)."...";
	}
	return $ret;
}

function sws_icon($result) {
	$type=strtolower($result['pubType']);
	$icon="web/graphics/jacket.jfif";
	
	switch($type) {
		case("ebook"): $icon="web/graphics/ebook.svg"; break;
		case("video recording"): $icon="web/graphics/video.svg"; break;
		case("video"): $icon="web/graphics/video.svg"; break;
		case("academic journal"): $icon="web/graphics/journal.svg"; break;
		case("periodical"): $icon="web/graphics/periodical.svg"; break;
		case("audio"): $icon="web/graphics/audio.svg"; break;
		case("audio recording"): $icon="web/graphics/audio.svg"; break;
		case("audiobook"): $icon="web/graphics/audio.svg"; break;
		//case("editorial & opinion"): $icon="web/graphics/ebook.svg"; break;
		//case("conference"):
		//case("report"):
		case("book"): $icon="web/graphics/book.svg"; break;
		default: $icon="web/graphics/jacket.jfif"; break;
		}
	return $icon;
}

function sws_cover($result,$size="M") {
	$type=strtolower($result['pubType']);
	$cover="web/graphics/jacket.jfif";
	if (($type=="book") || ($type=="ebook")) {
		$has_isbn=sws_get_isbn($result);
		if($has_isbn) {		
			$cover="//contentcafe2.btol.com/ContentCafe/jacket.aspx?UserID=ebsco-test&Password=ebsco-test&Return=T&Type=".$size."&Value=".$has_isbn[0];
		}
	} else { $cover=sws_icon($result); }
	return $cover;
}


function sws_modal($result, $counter, $recordLink,$recordCount) {
	$plink=$result['PLink'];
	$recordLink.="&recordcount=".$recordCount;
$modal= <<<EOT
<div class="modal fade" id="commentModal_$counter" style="padding:0px !important;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="background: transparent !important;">&times;</button>
          <h4 class="modal-title">Record Detail</h4>
        </div>
        <div class="modal-body">
			<div class='sws-record'>
				<iframe src="$recordLink&sws=$counter" class="sws-record-iframe" frameborder=0></iframe>
			</div>
		</div>
      </div>
    </div>
  </div>	
EOT;
	echo $modal;
}

// END CUSTOM FUNCTIONS



// Return the root directory relative to current PHP file
// which is /app
function root() {
     return dirname(__FILE__) . '/';
}

// Render a template
function render_template($locals, $fileName) {
    extract($locals);
    ob_start();
    include(root() . 'views/' . $fileName . '.php');
    return ob_get_clean();
}

// Render a view
function render($fileName, $templateName, $variableArray=array()) {
    $variableArray['content'] = render_template($variableArray, $fileName);
    print render_template($variableArray, $templateName);
}

// A basic pagination that displays maximum 10 pages
function paginate($recordCount, $limit, $page, $searchTerm, $fieldCode) {   
    $output = '';
    $linkCount =ceil($recordCount/$limit);
    if (!empty($page)) {
        if($page>$linkCount){
            $page = $linkCount;
        }   
    } else {
        $page = 1;
    }
    $base_url = "pageOptions.php?$searchTerm&fieldcode=$fieldCode";
    
    if($page%10 != 0){
    $f = floor($page/10);
    }
    else {
    $f=floor($page/10)-1;
    }
    $s = $page-1;
    if ($linkCount >= 1) {
        $output = '<div id="my-pagination">';
        if($s>0){
                $output .= "<a href=\"{$base_url}&pagenumber=GoToPage({$s})\"><span class='results-paging-previous'>&nbsp;&nbsp;&nbsp;&nbsp;</span></a>";
            }
    if($f < floor($linkCount/10)){
        for ($i = $f*10; $i < $f*10+10; $i++) {
            $p = $i+1;                     
            if ($p != $page) {
                $output .= "<a href=\"{$base_url}&pagenumber=GoToPage({$p})\"><u>{$p}</u></a>";
            } else {
                $output .= '<strong>'.$p.'</strong>';
            }          
        }
    }else{
        for ($i = $f*10; $i < $linkCount; $i++) {
            $p = $i+1;                    
            if ($p != $page) {
                $output .= "<a href=\"{$base_url}&pagenumber=GoToPage({$p})\">{$p}</a>";
            } else {
                $output .= $p;
            }        
        }
    }   $p_1 = $page+1;
     if($p_1 <= $linkCount){
        $output .= "<a href=\"{$base_url}&pagenumber=GoToPage({$p_1})\"><span class='results-paging-next'>&nbsp;&nbsp;&nbsp;&nbsp;</span></a>";
     }
        $output .= '<br class="clear" /></div>';
    }
    return $output;
}

?>