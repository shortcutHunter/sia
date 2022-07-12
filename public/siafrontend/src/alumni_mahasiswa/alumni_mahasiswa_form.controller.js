(function() {
  'use strict';

  angular
    .module('app.alumni')
    .controller('AlumniMahasiswaFormController', AlumniMahasiswaFormController);

  AlumniMahasiswaFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function AlumniMahasiswaFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Alumni';
    vm.table = 'alumni';

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getUrl('/alumni/get/data/session').then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
