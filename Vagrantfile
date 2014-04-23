VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "hashicorp/precise64"

    config.vm.network :private_network, ip: "192.168.254.254"
    config.vm.provision "ansible" do |ansible|
      ansible.playbook = "ansible/playbook.yml"
    end

    config.vm.synced_folder ".", "/vagrant", :nfs => true

end
