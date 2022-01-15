(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalAturPaket', modalAturPaket);

  modalAturPaket.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalAturPaket(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/atur_paket',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      activate();

      scope.aturPaket = aturPaket;
      scope.no_scroll = true;

      scope.$watch('data.paket_id', paketChanges, true);


      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function aturPaket() {
        let data = {
          paket_id: scope.data.paket_id
        };

        dataservice.postData('tahun_ajaran', data, scope.data.id).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }

      function paketChanges(newVal, oldVal) {
        if (newVal && scope.selectedData) {
          let selected_data = scope.selectedData['data.paket_id'];
          scope.data.paket = selected_data;
        }
      }
    }
  }
})();
