(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetDelete', widgetDelete);

  widgetDelete.$inject = ['dataservice', '$state'];

  /* @ngInject */
  function widgetDelete(dataservice, state) {
    var directive = {
      restrict: 'A',
      link: link
    };
    return directive;

    function link(scope, element, attrs, ctrl) {

      activate();

      function activate () {
        element.on('click', () => {
          swal({
            title: "Apakah anda yakin ingin menghapus data ini ?",
            icon: "warning",
            buttons: {
              cancel: true,
              text: "Iya"
            }
          })
          .then(willDelete => {
            if (willDelete) {
              let vm = scope.vm;
              let table = vm.table;
              let id = vm.data.id;
              
              dataservice.deleteRecord(table, id).then(function(data){
                state.go(vm.table);
              });
            }
          });
        });
      }

    };
  }
})();
