(function() {
  'use strict';

  angular
    .module('app.tahun_ajaran')
    .controller('TahunAjaranFormController', TahunAjaranFormController);

  TahunAjaranFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope'];
  /* @ngInject */
  function TahunAjaranFormController($q, dataservice, logger, stateParams, scope) {
    var vm = this;
    vm.title = 'Form Tahun Ajaran';
    vm.table = 'tahun_ajaran';

    activate();

    scope.$watch('vm.data.paket_id', paketChanges);

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          logger.info('Data loaded');
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
      }
    }
  }
})();
