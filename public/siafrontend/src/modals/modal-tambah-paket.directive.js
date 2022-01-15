(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalTambahPaket', modalTambahPaket);

  modalTambahPaket.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalTambahPaket(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/tambah_paket',
      require : 'ngModel',
      scope: {},
      replace: true
    };
    return directive;

    function link(scope, element, attr, ctrl) {
      activate();

      scope.addPaket = addPaket;
      scope.no_scroll = true;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function addPaket() {
        let paketData = ctrl.$modelValue || [];
        let data_to_push = angular.copy(scope.selectedData['vm.data.paket_id'].item);
        paketData = $.merge(paketData, data_to_push);
        ctrl.$setViewValue(paketData);
        $(element).modal('hide');
      }
    }
  }
})();
