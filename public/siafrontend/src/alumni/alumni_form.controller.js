(function() {
  'use strict';

  angular
    .module('app.alumni')
    .controller('AlumniFormController', AlumniFormController);

  AlumniFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function AlumniFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Alumni';
    vm.table = 'alumni';

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
