(function() {
  'use strict';

  angular
    .module('app.pmb')
    .controller('PmbFormController', PmbFormController);

  PmbFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PmbFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form PMB';
    vm.table = 'pmb';

    activate();

    function activate() {
      var promises = [getOption()];
      if (stateParams.dataId) {
        promises.push(getDataDetail());
      }
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

  }
})();
