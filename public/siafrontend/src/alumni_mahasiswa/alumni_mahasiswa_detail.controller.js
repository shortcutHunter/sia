(function() {
  'use strict';

  angular
    .module('app.alumni')
    .controller('AlumniMahasiswaDetailController', AlumniMahasiswaDetailController);

  AlumniMahasiswaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function AlumniMahasiswaDetailController($q, dataservice, logger, stateParams) {
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
      return dataservice.getUrl('/alumni/get/data/session').then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
