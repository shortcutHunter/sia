(function() {
  'use strict';

  angular
    .module('app.agama')
    .controller('AgamaFormController', AgamaFormController);

  AgamaFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function AgamaFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Agama';
    vm.table = 'agama';

    activate();

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
  }
})();
