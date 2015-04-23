<div class="errorwrapper error403">
	<div class="errorcontent">
        <h1>Site is under maintenance</h1>
        <h3>We'll be back soon.</h3>
        
        <p><strong>If we aren't back for too long, please drop message to 
			<?=CHtml::mailto(param('settings', 'adminEmail'), param('settings', 'adminEmail'))?></strong></p>
        <p>Meanwhile it's a good time to get a cup of coffee, to read a bok or to check email.<br />Please try again in an hour or less when the update should be complete.</p>
        <br />
        <button class="stdbtn btn_black" onclick="history.back()">Go Back to Previous Page</button> &nbsp; 
    </div><!--errorcontent-->
</div><!--errorwrapper-->