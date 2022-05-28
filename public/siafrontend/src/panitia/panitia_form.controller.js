(function() {
  'use strict';

  angular
    .module('app.panitia')
    .controller('PanitiaFormController', PanitiaFormController);

  PanitiaFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PanitiaFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Panitia';
    vm.table = 'panitia';

    activate();

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
  }
})();
