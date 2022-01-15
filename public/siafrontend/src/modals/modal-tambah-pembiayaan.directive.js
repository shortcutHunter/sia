(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalTambahPembiayaan', modalTambahPembiayaan);

  modalTambahPembiayaan.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalTambahPembiayaan(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/tambah_pembiayaan',
      require : 'ngModel',
      scope: {},
      replace: true
    };
    return directive;

    function link(scope, element, attr, ctrl) {
      activate();

      scope.addPembiayaan = addPembiayaan;
      scope.no_scroll = true;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function addPembiayaan() {
        let pembiayaanData = ctrl.$modelValue || [];
        let data_to_push = angular.copy(scope.vm.data);
        pembiayaanData.push(data_to_push);
        ctrl.$setViewValue(pembiayaanData);
        $(element).modal('hide');
      }
    }
  }
})();
