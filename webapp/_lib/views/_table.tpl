    <div id="page-wrapper">
        <div class="col-md-12 graphs">
        	<div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Table heading</th>
            <th>Table heading</th>
            <th>Table heading</th>
            <th>Table heading</th>
            <th>Table heading</th>
            <th>Table heading</th>
          </tr>
        </thead>
        <tbody>
<!--  iteration start  -->        	
         {if $table_value }
		 {foreach from=$table_value item=table_value}	
          <tr>
            <th scope="row">1</th>
            <td>{$table_value.<field_name>}</td>
            <td>Table cell</td>
            <td>Table cell</td>
            <td>Table cell</td>
            <td>Table cell</td>
            <td>Table cell</td>
          </tr>
          {/foreach}{/if}
<!--  iteration end  -->          
        </tbody>
      </table>
    </div><!-- /.table-responsive -->

    </div>
   </div>