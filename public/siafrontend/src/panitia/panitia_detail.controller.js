(function() {
  'use strict';

  angular
    .module('app.panitia')
    .controller('PanitiaDetailController', PanitiaDetailController);

  PanitiaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PanitiaDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Panitia';
    vm.table = 'panitia';
    vm.data = {};
    vm.getDataDetail = getDataDetail;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
