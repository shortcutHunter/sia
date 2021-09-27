(function() {
  'use strict';

  angular
    .module('app.agama')
    .controller('AgamaDetailController', AgamaDetailController);

  AgamaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function AgamaDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Agama';
    vm.table = 'agama';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
