<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link href="http://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" rel="stylesheet">

    <title>Prueba UTP</title>
  </head>
  <body>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="http://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    
    <div class="row" style="padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;">
        <button class="btn btn-outline-success importardata">Importar data</button>
        
    </div>
    <div class="row" style="padding-top:20px;padding-bottom:50px;padding-left:20px;padding-right:20px;text-align:center;">
        
        <div class="col-sm-12 table-responsive">
        
            <table class="table table-hover " id="tabRegistros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ISBN</th>
                        <th>TIPO_ITEM</th>
                        <th>EDITORIAL</th>
                        <th>AUTOR</th>
                        <th>TITULO</th>
                        <th>FECHA</th>
                    </tr>
                </thead>
                <tbody class="bodlis">
                </tbody>
            </table>
            
        </div>
    </div>
    <div class="row" style="padding-top:20px;padding-left:20px;padding-right:20px;text-align:center;">
        <div class="col-sm-12 table-responsive">
            <hr>
            <h5 class="itemsjm">Items</h5>
            <table class="table table-hover" style="padding-top:50px;" id="tabItems">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>TIPO_DE_ITEM</th>
                        <th>BIBLIOTECA</th>
                        <th>SIGNATURA</th>
                        <th>CODIGO_DE_BARRAS</th>
                    </tr>
                </thead>
                <tbody class="boditems">
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="modalimport">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form  enctype="multipart/form-data" id="formitem">
                @csrf
                <label>Importar items</label>
                <input type="file" name="file">

            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary ejecutar">Ejecutar</button>
            </div>
            </div>
        </div>
    </div>
    
  </body>
</html>
<script>
$(document).ready(function(){
    listartodo();
    var selec=[];
function listartodo(est){
    $.ajax({
		url : "fetch",
		type:'get',
		dataType:'json',
		data:{},
		success:function(resp){
            let html='';
            $.each(resp,function(i,item){
                html+='<tr data-id="'+item.biblionumber+'">';
                html+='<td>'+item.biblionumber+'</td>';
                html+='<td>'+item.isbn+'</td>';
                html+='<td>'+item.description+'</td>';
                html+='<td>'+item.publishercode+'</td>';
                html+='<td>'+item.author+'</td>';
                html+='<td>'+item.title+'</td>';
                html+='<td>'+item.timestamp+'</td>';
                html+='</tr>';
            })
            $(".bodlis").html(html);
            $('#tabRegistros').DataTable();
		}
	})
}
    $(document.body).off("click","#tabRegistros tbody tr");
    $(document.body).on("click","#tabRegistros tbody tr",function(e){
        e.preventDefault();
        let id=$(this).attr("data-id");
        $("#tabRegistros tbody tr").css("background-color","white");
        $(this).css("background-color","#D2C9F7");
        console.log(id);
        $.ajax({
            url : "detalle",
            type:'get',
            dataType:'json',
            data:{'id':id},
            success:function(resp){
                let html='';
                $.each(resp,function(i,item){
                    html+='<tr>';
                    html+='<td>'+item.itemnumber+'</td>';
                    html+='<td>'+item.description+'</td>';
                    html+='<td>'+item.branchname+'</td>';
                    html+='<td>'+item.itemcallnumber+'</td>';
                    html+='<td>'+item.barcode+'</td>';
           
                    html+='</tr>';
                })
                $(".itemsjm").text("Items(ID: "+id+")");
                $(".boditems").html(html);
                $('#tabItems').DataTable();
            }
        })
    });
    $(document.body).off("click",".importardata");
    $(document.body).on("click",".importardata",function(e){
        e.preventDefault();
        $("#modalimport").modal("show");
    });
    $(document.body).off("click",".ejecutar");
    $(document.body).on("click",".ejecutar",function(e){
        e.preventDefault();
        var formData = new FormData($("#formitem")[0]);
        $.ajax({
            url : "import",
            type:'post',
            data:formData,
            cache: false,
                contentType: false,
                processData: false,
            success:function(resp){
               console.log(resp);
            }
        })
    });
});
</script>