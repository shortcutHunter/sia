(function() {
  'use strict';

  angular
    .module('app.alumni')
    .controller('AlumniDetailController', AlumniDetailController);

  AlumniDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function AlumniDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Alumni';
    vm.table = 'alumni';
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
