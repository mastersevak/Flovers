<div class="errorwrapper error403">
	<div class="errorcontent">
        <h1>403 Forbidden Access</h1>
        <h3>Your have not permission to access this page.</h3>
        
        <p>This is likely to be caused by one of the following</p>
        <ul>
            <li>The author of the page has intentionally limited access to it.</li>
            <li>The computer on which the page is stored is unreachable.</li>
        </ul>
        <br />
        <button class="stdbtn btn_black" onclick="history.back()">Go Back to Previous Page</button> &nbsp; 
        <button onclick="location.href='<?=url('dashboard/back/index');?>'" class="stdbtn btn_orange">Go Back to Dashboard</button>
    </div><!--errorcontent-->
</div><!--errorwrapper-->