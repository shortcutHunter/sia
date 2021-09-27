(function() {
  'use strict';

  angular
    .module('app.sequance')
    .controller('SequanceFormController', SequanceFormController);

  SequanceFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function SequanceFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Sequance';
    vm.table = 'sequance';

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
