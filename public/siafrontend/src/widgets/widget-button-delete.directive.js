import swal from 'sweetalert';

(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetButtonDelete', widgetButtonDelete);

  widgetButtonDelete.$inject = ['dataservice', '$location', '$state', 'logger'];

  /* @ngInject */
  function widgetButtonDelete(dataservice, location, state, logger) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      let vm = scope.$parent.vm;
      let current_state = state.router.globals.current;
      let state_name = current_state.name;
      state_name = state_name.split("_form").shift();
      state_name = state_name.split("_detail").shift();

      element.on('click', function(){
        swal({
          title: "Anda yakin ingin menghapus record ini ?",
          icon: "warning",
          buttons: {
            cancel: true,
            confirm: true
          }
        })
        .then(willDelete => {
          if (willDelete) {
            deleteRecords();
          }
        });
      });


      function deleteRecords () {
        dataservice.deleteRecord(vm.table, vm.data.id).then(function(data){
          logger.success("Berhasil delete record");
          location.path(`/${state_name}`);
        });
      }
    }
  }
})();
