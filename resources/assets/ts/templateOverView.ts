import * as $ from "jquery"
import * as bootbox from "bootbox";

import {translate} from "./Translate";
$(()=>{
  $("#templateOverviewFilter").on("keyup", (event) => {
    let value = $(event.target).val().toLowerCase();
    $("#templateOverviewTable tr").each(function (index, elem) {
      $(elem).toggle($(elem).text().toLowerCase().indexOf(value) > -1)
    });
  });
  $('.selectpicker').selectpicker({
    style: 'btn btn-default',
    liveSearch:true
  });

  $('.delete-template').on('click', (event) =>  {
    let templateId = $(event.target).data('id');

    // Initialise modal and show loading icon and message
    var dialog = <any> bootbox.confirm({
      title: '<h4 class="alert alert-danger">' + translate('deleteTemplate') + '</h4>',
      size: 'large',
      message: '<p>' + translate('deleteTemplateMessage') +  '</p>',
      buttons: {
        confirm: {
          label:'<span class="glyphicon glyphicon-ok" ></span>',
          className:'btn-danger'
        },
        cancel: {
          label:'<span class="glyphicon glyphicon-remove" ></span>',
          className: 'btn-success'
        }
      },
      callback:(result) => {
        if(result){
          $('#delete-template-' + templateId).submit();
        }
      }
    });
  });
});
