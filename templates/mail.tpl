{if $MAILS_ID eq null}
{assign var='MAILS_ID' value=$VARS.SENDMAILS_ID}
{/if}
<div class="sendMails">
   <a href="{$THIS_PAGE_LINK}#mailEdit" id="mailEditButton">
      {$VARS.MAIL_FORM_SHOW}
      {if $VARS.MAIL_FORM_SHOW_SUBNAME[$MAILS_ID] neq null}
      - {$VARS.MAIL_FORM_SHOW_SUBNAME[$MAILS_ID]}
      {/if}
   </a>
   <div id="mailEditTabs">
      <ul class="editMailTabs langMenu">
         <li><a href="#mailAddress" class="selected">{$VARS.MAILS_LABEL}</a></li>
         {if $VARS.IS_MAIL_TEXT[$MAILS_ID]}
         <li><a href="#editMail" class="selected">{$VARS.EDIT_MAIL_LABEL}</a></li>
         {/if}
      </ul>
      <div id="mailAddress" class="tabcontent">
         <form action="{$THIS_PAGE_LINK}" method="post">
            <label>{$VARS.MAIL_NAME}:</label>
            <input name="sendmail_newmail" type="text" value="" size="30" maxlength="100" />
            <input name="sendmail_newmailsend" type="submit" value="{$VARS.BUTTON_SENDMAIL_SEND}" />
         </form>
         <table border="0" cellpadding="2" cellspacing="2">
            <tbody>
               {foreach from=$VARS.SENDMAILS_ARRAY[$MAILS_ID] item="MAIL"}
               <tr>
                  <td>{$MAIL.mail}</td>
                  <td align="right">
                     <form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.CONFIRM_MESAGE_DELETE_MAIL} - {$MAIL.mail}')">
                        <input type="hidden" name="sendmail_id" value="{$MAIL.id_mail}" />
                        <input type="submit" name="sendmail_delete" value="{$VARS.BUTTON_SENDMAIL_DELETE}" />
                     </form>
                  </td>
               </tr>
               {foreachelse}
               <tr>
                  <td colspan="2" align="center">
                     {$VARS.NOT_ANY_MAIL}
                  </td>
               </tr>
               {/foreach}
            </tbody>
         </table>
      </div>
      {if $VARS.IS_MAIL_TEXT[$MAILS_ID]}
      <div id="editMail" class="tabcontent">
         <form action="{$THIS_PAGE_LINK}" method="post">
            <label>{$VARS.MAIL_SUBJECT}:</label><br />
            <input name="sendmail_content_subject" type="text" value="{$VARS.MAIL_TEXT_DETAIL[$MAILS_ID].subject}" size="30" maxlength="100" /><br />
            <label>{$VARS.MAIL_CONTENT}:</label><br />
            <textarea name="sendmail_content_text" cols="50" rows="10">{$VARS.MAIL_TEXT_DETAIL[$MAILS_ID].text}</textarea><br />
            <input name="sendmail_content_send" type="submit" value="{$VARS.BUTTON_SENDMAIL_SEND}" />
         </form>
         <p>
            <span>{$VARS.TAGS}:</span><br />
            {foreach from=$VARS.MAIL_TAGS[$MAILS_ID] key=TAGNAME item="TAG"}
            {if $TAG.type eq 2}
            %{$TAGNAME}[true/false]% - {$TAG.label}<br />
            {else}
            %{$TAGNAME}% - {$TAG.label}<br />
            {/if}
            {/foreach}

         </p>
      </div>
      {/if}
   </div>
   {literal}
   <script type="text/javascript">
      $("div#mailEditTabs").css('display', 'none');
      $(document).ready(function(){
         $("div#mailEditTabs").tabs();
      });
      $("a#mailEditButton").click(function () {
         $("div#mailEditTabs").toggle("fast");
         return false;
      });
   </script>
   {/literal}
   <!--
<h5>{$VARS.SENDMAIL_LABEL_NAME} ({$VARS.SENDMAILS_NUM_ROWS[$MAILS_ID]})</h5>
<form method="post" action="{$THIS_PAGE_LINK}">
   <input type="text" name="sendmail_mail" value="{$VARS.SEND_MAIL}" size="28" maxlength="60" />
   <input type="submit" name="sendmail_send" value="{$VARS.BUTTON_SENDMAIL_SEND}"/>
</form>

<table border="0" cellpadding="2" cellspacing="2">
<thead>
   <tr>
      <th colspan="2" align="left">{$VARS.MIAL_NAME}:</th>
   </tr>
</thead>
{foreach from=$VARS.SENDMAILS_ARRAY[$MAILS_ID] item="MAIL"}

<tbody>
   <tr>
   <td>{$MAIL.mail}</td>
   <td align="right">
      <form action="{$THIS_PAGE_LINK}" method="post" onsubmit="return Confirm('{$VARS.CONFIRM_MESAGE_DELETE} - {$MAIL.mail}')">
         <input type="hidden" name="sendmail_id" value="{$MAIL.id_mail}" />
         <input type="submit" name="sendmail_delete" value="{$VARS.BUTTON_SENDMAIL_DELETE}" />
      </form>
   </td>
   </tr>
</tbody>
{foreachelse}
<tr>
   <td colspan="2" align="center">
      {$VARS.NOT_ANY_MAIL}
   </td>
</tr>
{/foreach}
</table>
<br />
   -->
</div>