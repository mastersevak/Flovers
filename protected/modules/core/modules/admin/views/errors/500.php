<div class="errorwrapper error403">
	<div class="errorcontent">
        <h1>500 Internal Server Error</h1>
        <h3>The server encountered an internal error and was unable to complete your request.</h3>
        
        <p>Please contact the server administrator <strong>webmaster@yourdomain.com</strong> and informed them of the time the error occurred.<br /> More information about this error may be available in the server error log.</p>
        
		<p><?=$message?></p>
        <br />
        <button class="stdbtn btn_black" onclick="history.back()">Go Back to Previous Page</button> &nbsp; 
        <button onclick="location.href='<?=url('dashboard/back/index');?>'" class="stdbtn btn_orange">Go Back to Dashboard</button>
    </div><!--errorcontent-->
</div><!--errorwrapper-->