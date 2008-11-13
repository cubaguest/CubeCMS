{if $CH_ID eq null}
{assign var='CH_ID' value=$VARS.CHANGES_ID}
{/if}
<h5 id="bobcontent{$CH_ID}-title" class="handcursor">{$VARS.CHANGES_LABEL_NAME} ({$VARS.CHANGES_NUM_ROWS[$CH_ID]})</h5>
<div id="bobcontent{$CH_ID}" class="switchgroup{$CH_ID} changes">
{foreach from=$VARS.CHANGES_ARRAY[$CH_ID] item="CHANGE"}
{* $CHANGE.time|date_format:"%d.%m.%Y %R" *}
<b>{$CHANGE.time|date_format:"%x %R"} {if $CHANGE.surname neq null}{$CHANGE.surname}, {$CHANGE.name}{else}{$CHANGE.username}{/if}</b> - {$CHANGE.label}<br />
{/foreach}
</div>


{literal}
<script type="text/javascript">
// MAIN FUNCTION: new switchcontent("class name", "[optional_element_type_to_scan_for]") REQUIRED
// Call Instance.init() at the very end. REQUIRED

var bobexample=new switchcontent("switchgroup{/literal}{$CH_ID}{literal}", "div") //Limit scanning of switch contents to just "div" elements
bobexample.setStatus('<img src="./images/buttons/collapse3.gif" /> ', '<img src="./images/buttons/expand3.gif" /> ')
bobexample.setColor('darkred', 'black')
bobexample.setPersist(true)
bobexample.collapsePrevious(true) //Only one content open at any given time
bobexample.init()
</script>
{/literal}
{* $CH_ID *}
{* přeskočení na další změny *}
{assign var='CH_ID' value=`$CH_ID+1`}
