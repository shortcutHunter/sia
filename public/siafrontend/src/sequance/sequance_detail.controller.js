(function() {
  'use strict';

  angular
    .module('app.sequance')
    .controller('SequanceDetailController', SequanceDetailController);

  SequanceDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function SequanceDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Sequance';
    vm.table = 'sequance';
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
