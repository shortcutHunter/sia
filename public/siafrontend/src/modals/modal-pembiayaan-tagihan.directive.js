(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalPembiayaanTagihan', modalPembiayaanTagihan);

  modalPembiayaanTagihan.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalPembiayaanTagihan(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/pembiayaan_tagihan',
      require : 'ngModel',
      scope: {},
      replace: true
    };
    return directive;

    function link(scope, element, attr, ctrl) {
      activate();

      scope.addPembiayaan = addPembiayaan;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function addPembiayaan() {
        let pembiayaanData = ctrl.$modelValue || [];
        let data_to_push = angular.copy(scope.selectedData['vm.data.item_id']);
        // delete data_to_push['id'];
        pembiayaanData.push(data_to_push);
        ctrl.$setViewValue(pembiayaanData);
        $(element).modal('hide');
      }
    }
  }
})();
