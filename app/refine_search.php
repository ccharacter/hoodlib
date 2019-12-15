            <div><h4>Refine Search</h4></div>
            
<?php if(!empty($results['appliedFacets'])||!empty($results['appliedLimiters'])||!empty($results['appliedExpanders'])){ ?>
<div class="filters">
    <strong>Current filters</strong>
    <ul class="filters">
<!-- applied facets -->
        <?php if (!empty($results['appliedFacets'])) { ?>
        <?php foreach ($results['appliedFacets'] as $filter) { ?>
        <?php foreach ($filter['facetValue'] as $facetValue){ 
              $action = http_build_query(array('action'=>$facetValue['removeAction']));
        ?>
        <li>
        <a href="<?php echo $refineSearchUrl.'&'.$queryStringUrl.'&'.$action; ?>">                 
            <img  src="web/delete.png"/>                      
        </a>
        <a href="<?php echo $refineSearchUrl.'&'.$queryStringUrl.'&'.$action; ?>"><?php echo $facetValue['Id']; ?>: <?php echo $facetValue['value']; ?></a>
        </li>
        <?php } } }?>
<!-- Applied limiters -->
        <?php if (!empty($results['appliedLimiters'])) { ?>    
        <?php foreach ($results['appliedLimiters'] as $filter) {
                  $limiterLabel = '';
                  $filterAddOn = '';
                  foreach($Info['limiters'] as $limiter){
                      if($limiter['Id']==$filter['Id']){
                          $limiterLabel = $limiter['Label'];
                          if($filter['Id'] == 'DT1'){
                            $filterDate = explode('/',$filter['limiterValue']['value']);
                            $filterAddOn = '['.substr($filterDate[0],0,4).'-'.substr($filterDate[1],0,4).']';
                          }
                          break;
                      }
                  }
                  $action = http_build_query(array('action'=>$filter['removeAction']));
        ?>
        <li>
        <a href="<?php echo $refineSearchUrl.'&'.$queryStringUrl.'&'.$action; ?>">                 
            <img  src="web/delete.png"/>                      
        </a>
        <a href="<?php echo $refineSearchUrl.'&'.$queryStringUrl.'&'.$action; ?>">Limiter: <?php echo $limiterLabel.' '.$filterAddOn; ?></a>
        </li>
        <?php } }?>        
<!-- Applied expanders -->
        <?php if (!empty($results['appliedExpanders'])) { ?>
        <?php foreach ($results['appliedExpanders'] as $filter) {
                    $expanderLabel = '';
					if (isset($Info['expanders'])) {
						foreach($Info['expanders'] as $exp){
							if($exp['Id']==$filter['Id']){
								$expanderLabel = $exp['Label'];
								break;
							}
						}
						$action = http_build_query(array('action'=>$filter['removeAction']));
					}
             ?>
        <li>
        <a href="<?php echo $refineSearchUrl.'&'.$queryStringUrl.'&'.$action; ?>">                 
            <img  src="web/delete.png"/>                      
        </a>
        <a href="<?php echo $refineSearchUrl.'&'.$queryStringUrl.'&'.$action; ?>">Expander: <?php echo $expanderLabel; ?></a>
        </li>
        <?php } } ?>        
    </ul>
</div>
<?php } ?>
<?php if(!empty($Info['limiters'])){?>
<div class="facets" style="font-size: 80%">
                <dl class="facet-label">
                    <dt>Limit your results</dt>
                </dl>
                <dl class="facet-label" >
                    <form action="limiter.php" method="get">
                   <?php for($i=0;$i<3;$i++){ ?>
                   <?php   $limiter=$Info['limiters'][$i]; ?>
                     <?php if($limiter['Type'] =='select'){?>
                      <?php if(empty($results['appliedLimiters'])){ ?>
                      <dd><input type="checkbox" value="<?php echo $limiter['Action'];?>" name="<?php echo $limiter['Id']; ?>" /><?php echo $limiter['Label'] ?></dd> 
                      <?php }else{
                                 $flag = FALSE;
                                 foreach($results['appliedLimiters'] as $filter){
                                    if($limiter['Id']==$filter['Id']){ 
                                        $flag = TRUE;
                                        break;
                                    }
                                 }    
                               if($flag==TRUE){ ?>
                                      <dd><input type="checkbox" value="<?php echo $limiter['Action'];?>" name="<?php echo $limiter['Id']; ?>" checked="checked" /><?php echo $limiter['Label'] ?></dd>                               
                      <?php  }else{ ?>
                                      <dd><input type="checkbox" value="<?php echo $limiter['Action'];?>" name="<?php echo $limiter['Id']; ?>" /><?php echo $limiter['Label'] ?></dd> 
                      <?php }}}}?>
                    <input type="hidden" value="<?php echo $searchTerm;?>" name="query" />
                    <input type="hidden" value="<?php echo $fieldCode;?>"  name="fieldcode" />
                    <input type="submit" class="sws-button" value="Update" />
                    </form>               
                </dl>              
</div>
<?php } ?>
<?php if(!empty($results['dateRange'])){ ?>
<div class="facet" style="font-size: 80%">
    <dl class="facet-label">
        <dt>Date Published</dt>
    </dl>
    <dl class="facet-label">
        <dd>
            <div id="slider-range"></div>
            <div id="date-boxes">
            <input type="text" name="minDate" id="minDate" value="<?php echo substr($results['dateRange']['MinDate'],0,4);?>" size="4"/><span id="date-hypen"><center>-</center></span><input type="text" name="maxDate" id="maxDate" value="<?php echo substr($results['dateRange']['MaxDate'],0,4);?>" size="4"/>
            </div>
            <form action="limiter.php" method="get" onsubmit="dateRangeSubmit(this)">
            <input type="hidden" name="DT1" id="DT1" value="addlimiter(DT1:<?php echo $results['dateRange']['MinDate'];?>/<?php echo $results['dateRange']['MaxDate'];?>)" />
            <input type="hidden" value="<?php echo $searchTerm;?>" name="query" />
            <input type="hidden" value="<?php echo $fieldCode;?>"  name="fieldcode" />
            <input type="submit" class="sws-button" value="Update" id="date-submit">
            </form>            

        </dd>
        <script>
            $(function() {
                $("#slider-range").slider({
                range: true,
                min: <?php echo substr($results['dateRange']['MinDate'],0,4);?>,
                max: <?php echo substr($results['dateRange']['MaxDate'],0,4);?>,
                values: [ <?php echo substr($results['dateRange']['MinDate'],0,4);?>, <?php echo substr($results['dateRange']['MaxDate'],0,4);?> ],
                slide: function( event, ui ) {
                    $("#minDate").val(ui.values[0]);
                    $("#maxDate").val(ui.values[1]);
                    $("#DT1").val('addlimiter(DT1:'+ui.values[0]+'-01/'+ui.values[1]+'-12)');
                }
                });
            } );
            </script>
    </dl>
</div>
<?php } ?>
<div class="facet" style="font-size: 80%">
                <dl class="facet-label">
                    <dt>Expand your results</dt>
                </dl>
                <dl class="facet-label">
                <form action="expander.php">
                    <?php 
					foreach($Info['expanders'] as $exp){
                       if(empty($results['appliedExpanders'])){ ?>
                           <dd><input type="checkbox" value="<?php echo $exp['Action'];?>" name="<?php echo $exp['Id']; ?>" /><?php echo $exp['Label'];?></dd>
                    <?php }else{
                        $flag = FALSE;
                        foreach($results['appliedExpanders'] as $aexp){
                            if($aexp['Id']==$exp['Id']){
                                $flag=TRUE;
                                break;
                            }
                        }
                        
                        if($flag==TRUE){ ?>
                           <dd><input type="checkbox" value="<?php echo $exp['Action'];?>" name="<?php echo $exp['Id']; ?>"  checked="checked"/><?php echo $exp['Label'];?></dd>
                   <?php }else{ ?>
                            <dd><input type="checkbox" value="<?php echo $exp['Action'];?>" name="<?php echo $exp['Id']; ?>" /><?php echo $exp['Label'];?></dd>
                   <?php   }
                    } 
                    }?>                 
                    <input type="hidden" value="<?php echo $searchTerm;?>" name="query" />
                    <input type="hidden" value="<?php echo $fieldCode;?>"  name="fieldcode" />
                    <input type="submit" class="sws-button" value="Update"/>
                </form>
                </dl>
</div>            
<?php if (!empty($results['facets'])) { $i=0; ?>
    <div class="facets">
        <?php 
		foreach ($results['facets'] as $facet) { $i++; 
			if(!empty($facet['Label'])){ ?>
				<script type="text/javascript">            
					jQuery(document).ready(function(){             
						 jQuery("#flip<?php echo $i ?>").click(function(){              
							 jQuery("#panel<?php echo $i ?>").slideToggle("slow");
							 if(jQuery("#plus<?php echo $i ?>").html()=='[+]'){
								 jQuery("#plus<?php echo $i ?>").html('[-]');
							 }else{
								 jQuery("#plus<?php echo $i ?>").html('[+]');
							 }
						 });   
					});
				</script>
        
				<div class="facet" style="font-size: 80%">                
					<dl class="facet-label" id="flip<?php echo $i ?>">
						<dt><span style="font-weight: lighter" id="plus<?php echo $i ?>">[+]</span><?php echo $facet['Label']; ?></dt>
					</dl>
					<dl class="facet-values" id="panel<?php echo $i ?>">
	   
						<?php 
							foreach ($facet['Values'] as $facetValue) { 
								$action = http_build_query(array('action'=>$facetValue['Action']));
								echo '<dd>'
									.'	<a href="'.$refineSearchUrl.'&'.$queryStringUrl.'&'.$action.'">'.$facetValue['Value'].'</a>('.$facetValue['Count'].')'
									.'</dd>'
									;
							} 
						?>                  
					</dl>
				</div>
          <?php } ?>
        <?php } ?>
    </div>
<?php } ?>