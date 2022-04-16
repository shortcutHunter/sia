(function() {
  'use strict';

  angular
    .module('app.kwitansi')
    .controller('KwitansiFormController', KwitansiFormController);

  KwitansiFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope'];
  /* @ngInject */
  function KwitansiFormController($q, dataservice, logger, stateParams, compile, scope) {
    var vm = this;
    vm.title = 'Form Kwitansi';
    vm.table = 'kwitansi';
    vm.data = {};

    activate();

    scope.$watch('vm.data.paket_id', paketChanges);

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function paketChanges(newVal, oldVal) {
      if (newVal) {
        let selected_data = scope.selectedData['vm.data.paket_id'];
        vm.data.paket = selected_data;
        vm.data.nominal = vm.data.paket.nominal;
      }
    }
  }
})();
