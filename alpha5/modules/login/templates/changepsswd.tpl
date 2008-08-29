<h2>{$VARS.LOGIN_CHANGE_PASSWORD_NAME}</h2>
<div class="edit_form">
<form method="post" action="{$THIS_PAGE_LINK}">
   <table>
   <tbody>
      <tr>
         <td width="150px">{$VARS.OLD_PSSWD}:</td>
         <td>
            <input type="password" name="passwd_old" size="20" maxlength="20" alt="{$VARS.OLD_PSSW}" />
         </td>
      </tr>
      <tr>
         <td>{$VARS.NEW_PSSWD}:</td>
         <td>
            <input type="password" name="passwd_new" size="20" maxlength="20" alt="{$VARS.NEW_PSSWD}" />
         </td>
      </tr>
      <tr>
         <td>{$VARS.NEW_PSSWD_CONFIRM}:</td>
         <td>
            <input type="password" name="passwd_new_confirm" size="20" maxlength="20" alt="{$VARS.NEW_PSSWD_CONFIRM}" />
         </td>
      </tr>
      <tr>
         <td colspan="2"  align="right">
            <input type="submit" name="passwd_change" value="{$VARS.SEND_PASSWD_BUTTON}" alt="{$VARS.SEND_PASSWD_BUTTON}" />
       	</td>
      </tr>
      <tr>
         <td><input type="button" value="{$VARS.GENERATE_PSSWD_BUTTON}" onclick="suggestPassword()" /></td>
         <td><input type="text" name="generated_pw" id="generated_pw" /></td>
      </tr>
  </tbody>
</table>
</form>
</div>