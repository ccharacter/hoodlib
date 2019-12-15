<?php 
$queryStringUrl = $results['queryString'];

$swsItems=array();

//error_log("---------------NEW PAGE HERE---------------");
//error_log($queryStringUrl,0);
//error_log("---------------NEW PAGE HERE---------------");

//change search term for refinement of autocorrected searches
if(isset($results['autoCorrect']) && !empty($results['autoCorrect'])){
    $searchTerm = $results['autoCorrect'][0];
}

if(isset($_REQUEST['insidejournal']) && !empty($_REQUEST['insidejournal'])){
    $insidejournal = 'within: <i>'.base64_decode($_REQUEST['insidejournal']).'</i>';
}
else{
    $insidejournal = '';
}

// URL used by facets links
$refineParams = array(
    'refine' => 'y',
    'query'  => $searchTerm,
    'fieldcode' => $fieldCode
);
$refineParams = http_build_query($refineParams);
$refineSearchUrl = "results.php?".$refineParams;
$encodedSearchTerm = http_build_query(array('query'=>$searchTerm));
$encodedHighLigtTerm = http_build_query(array('highlight'=>$searchTerm));
?>
<div id="toptabcontent">
    <div class="topSearchBox">
        <form action="results.php" id="searchform">
    <p>
        <input type="text" name="query" id="lookfor" value="<?php echo $searchTerm ?>"/>  
        <input type="hidden" name="expander" value="fulltext" />
        <?php 
        $selected1 = '';
        $selected2 = '';
        $selected3 = '';
        if($fieldCode == 'keyword'){
            $selected1 = "selected = 'selected'";
        } 
        if($fieldCode == 'AU'){
            $selected2 = "selected = 'selected'";
        }
        if($fieldCode == 'TI'){
            $selected3 = "selected = 'selected'";
        } ?>
        <select name="fieldcode">
			<option id="type-keyword" name="fieldcode" value="keyword" <?php echo $selected1 ?> >Keyword</option>
            <option id="type-author" name="fieldcode" value="AU"<?php echo $selected2; ?> >Author</option>
            <option id="type-title" name="fieldcode" value="TI"<?php echo $selected3 ?> >Title</option>                
        </select>
        <input type="submit" class='sws-button' value="Search" />
        
    </p>
    <?php
    //support autoCorrected Search and AutoSuggest
    if(isset($results['autoCorrect']) && !empty($results['autoCorrect'])){
        ?>
        <div class="autocorrectsuggest">
            <div class="autocorrectedterm">We automatically corrected your search to:
                <?php
                foreach($results['autoCorrect'] as $suggestion) {
                    $query = $_REQUEST;
                    $query['query'] = (string)$suggestion;
                    $query['autocorrect'] = 'n';
                    $newQuery = http_build_query($query);
                    echo '<a href="?'.$newQuery.'">'.$suggestion.'</a>';
                    if(count($results['autoCorrect']) > 1 && $ac < count($results['autoCorrected'])){
                      echo '; ';
                    }
                    $ac++;
                  }
                ?>
            </div>
            <div class="autocorrectedoriginal">Search for your original query instead:
                <?php
                foreach($results['autoSuggest'] as $suggestion){
                    $query = $_REQUEST;
                    $query['query'] = (string)$suggestion;
                    $query['autocorrect'] = 'n';
                    $newQuery = http_build_query($query);
                    echo '<a href="?'.$newQuery.'">'.$suggestion.'</a>';
                    if(count($results['autoSuggest']) > 1 && $as < count($results['autoSuggest'])){
                    echo '; ';
                    }
                    $as++;
                }
                ?>
            </div> 
        </div>
        <?php
    }
    elseif(isset($results['autoSuggest']) && !empty($results['autoSuggest'])){?>
        <div class="autocorrectsuggest">
            <div class="autosuggestedterms">Did you mean:
                <?php
                foreach($results['autoSuggest'] as $suggestion){
                    $as = 0;
                    $query = $_REQUEST;
                    $query['query'] = (string)$suggestion;
                    $newQuery = http_build_query($query);
                    echo '<a href="?'.$newQuery.'">'.$suggestion.'</a>';
                    if(count($results['autoSuggest']) > 1 && $as < count($results['autoSuggest'])){
                    echo '; ';
                    }
                    $as++;
                }
                ?>
            </div> 
        </div>
    <?php
    }
    ?>
    </form>
    </div>
<div class="table">
    <div class="table-row">
        <div class="table-cell sws-left">         
			<?php include("app/refine_search.php"); ?>
        </div>
<div class="table-cell sws-right">
<?php 
	if($debug=='y'){
		echo '<div style="float:right"><a target="_blank" href="debug.php?result=y">Search response XML</a></div>';
	} ?>
	<div class="top-menu">
		<h2>Results</h2> 
		<?php include("app/results_top.php"); ?>
	<?php if (empty($results['records'])) { ?>
        <div class="result table-row">
            <div class="table-cell">
                <h2><i>No results were found.</i></h2>
            </div>
        </div>
    <?php } else { ?>
<div id="primary-content-wrap" class="wrap clearfix">
<?php include("app/tile_pane.php"); ?>
<!--	<div class="sidebar">
		<div class="floating-div" id="sws-details">
			<h3>Mouse over items at left to view details.</h3>
			<h3>Click one to see its full record; to check availability, click the VIEW IN EDS link.</h3>
			<h3>Chrome users: resource links (i.e. VIEW IN EDS) may not work in "incognito" mode.</h3>
		</div>
	  </div>
</div>-->
 	<?php } 
	// show full result of last item
	//error_log(print_r($result,true),0); ?>
</div>
<footer>
<?php if (!empty($results)) { ?>
        <div class="pagination"><?php echo paginate($results['recordCount'], $limit, $start, $encodedSearchTerm, $fieldCode); ?></div>       

<?php } ?>
        </div>
</footer>
<!--<script type="text/javascript" src="web/custom_javascript.js"></script>-->

  </div>
</div>
</div>      
</div>

<?php


function buildResearchStarterPlacard($relRec, $count, $fieldCode, $encodedHighLigtTerm, $encodedSearchTerm){
    $rs = $relRec['records'][0];
        
        $rsHtml ='<div class="related-content bluebg" id="related-content">';
        if(isset($rs["ImageInfo"]) && !empty($rs["ImageInfo"])){
            $rsHtml .= '<img alt="" src="'.$rs["ImageInfo"].'" id="rsimg">';
        } 
        $rsHtml .='<span class="rsIntro">'.$relRec['Label'].': </span>';

        foreach($rs["Items"] as $item) {
            if ($item["Group"]=="Au") {continue;}
            if ($item["Group"]=="Su") {continue;}
            if ($item["Group"]=="Src") {continue;}
            switch ($item["Label"]) {
                case "Title":
                    $rsHtml.='<span class="rstitle">'.$item["Data"].'</span><br/>';
                    break;						
                case "Abstract":
                    $l="researchstarter.php?db=".$rs['DbId']."&an=".$rs['An']."&".$encodedHighLigtTerm."&".$encodedSearchTerm."&fieldcode=".$fieldCode;                         
                    $rsHtml.='<span>';
                    if(strlen($item["Data"]) > 275) {
                        $rsHtml .= mb_substr(str_replace('...','',$item["Data"]),0,275).'&hellip;&nbsp;(<a href="'.$l.'">more</a>)';
                    }
                    else {
                        $rsHtml .= $item["Data"].'(<a href="'.$l.'">more</a>)';
                    }
                    $rsHtml .= '</span><br/>';
                    break;
                default:
                    $rsHtml.='<span>'.$item["Data"].'</span><br/>';
            }
        }
        
        foreach($rs["Items"] as $item) {
            switch ($item["Group"]) {			
                case "Src":
                    $rsHtml.='<span class="rsSource">'.$item["Data"].'</span><br/>';
                    break;
            }
        }	
        if($count > 0){
            $rsHtml.='<div id="showEMP"><a href="javascript:showEMP();">We also found an exact Publication Match, click here to see it!</a></div>';
        }
        $rsHtml.='<span style="clear:both"/>';
        $rsHtml.='</div>';
        echo $rsHtml;
}

function buildExactMatchPlacard($relRec, $count){
    if($count > 0){
        $hideempplacard = 'style="display:none"';
    }
    else{
        $hideempplacard = '';
    }
    $empHtml = '<div id="emp_placard" class="emp_placard yellowbg" '.$hideempplacard.'>';
    $empHtml .= '<div class="emp_label">'.$relRec['Label'].'</div>';
    foreach($relRec['records'] as $rec){
        $empHtml .= '<div class="emp_title"><a href="'.$rec['PLink'].'" target="_blank">'.$rec['Title'].'</a></div>';

        if($rec['IsSearchable'] == 'y'){
            $empHtml .= '<div class="emp_sb">';
            $empHtml .= '<form action="results.php" method="get">';
            $empHtml .= '<input type="hidden" name="search" value="y">';
            $empHtml .= '<input type="hidden" name="type" value="keyword">';
            $empHtml .= '<input type="hidden" name="publicationid" value="'.$rec['PublicationId'].'">';
            $empHtml .= '<input type="text" name="query" size="40" placeholder="Search Inside this Journal" id="pubinsidesearch" autocomplete="off">';
            $empHtml .= '<input type="hidden" name="insidejournal" value="'.base64_encode($rec['Title']).'">';
            $empHtml .= '<button type="submit" id="pubinsidebutton">Go</button>';
            $empHtml .= '</form>';
            $empHtml .= '</div>';
        }

        if(count($rec['FullText']) > 0){
            $empHtml .= '<div class="emp_ft_target">';
            $empHtml .= '<div id="emp_show_ft_list"><a href="javascript:showEmpFtList();">[+]Show Full Text Access Options</a></div>';
            $empHtml .= '<div id="emp_hide_ft_list" style="display:none"><a href="javascript:hideEmpFtList();">[-]Hide Full Text Access Options</a></div>';
            $empHtml .= '<ul id="emp_ft_list" style="display:none">';
            foreach($rec['FullText'] as $fullTxt){
                $empHtml .= '<li><a href="'.$fullTxt['URL'].'" target="_blank">'.$fullTxt['Name'].'</a></li>';
            }
            $empHtml .= '</ul>';
            $empHtml .= '</div>';
        }
    }
    if($count > 0){
        $empHtml.='<div id="showRS"><a href="javascript:showRS();">We also found a Research Starter, click here to see it!</a></div>';
    }
  $empHtml .= '</div>';
  echo $empHtml;
}


//purpose of this functino is to avoid overly long list of customer names, usually caused by the institutional affiliation being shown
//due to the specifics of the author field, a few tests are necessary to break this apart properly

function handleResultListAuthors($result, $number = 5){
    
    //debug function for testing, to be removed before launch
    if(isset($_GET['debug']) && $_GET['debug'] == 'y'){
        foreach($result['Items']['Au'] as $Author){ 
            echo $Author['Data'];
        }
        echo '<hr>';
    }

    $c = 1;
    $authorList = [];
    $dump = 0;

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


    $totalAuthors = count($authorList);
    foreach($authorList as $list){
        if($c <= $number){
            echo $list;
            if($c < $number){
                echo '; ';
            }
            elseif($c == $number && $totalAuthors > $number && $dump == 0){
                echo '; et&nbsp;al.';
            }
            $c++;
        }
    }
    
}
?>