#!/bin/bash



# Revoke Client certificate
function revoke_cert()
{
	echo "Revoking certificate and keys for client: $1";
	docker-compose -f /opt/docker/stacks/openvpn/vpn.compose.yml run --rm openvpn ovpn_revokeclient $1 remove
	rm -v $HOME/.secure/vpn-certs/$1.ovpn;
}

# Export client certificate
function export_cert()
{
	exportpath="$HOME/.secure/vpn-certs/$1.ovpn";
	echo "Exporting certificate for client: $1 to $exportpath";
	docker-compose -f /opt/docker/stacks/openvpn/vpn.compose.yml run --rm openvpn ovpn_getclient $1 > $exportpath
}

# Generate client certificate
function gen_cert()
{
	echo "Generating client certificate for client - $1"
	docker-compose -f /opt/docker/stacks/openvpn/vpn.compose.yml run --rm openvpn easyrsa build-client-full $1 nopass
	export_cert $1;
}

# Prompt user for client name
function get_client()
{
	read -p 'Input client name: ' clientname;
	echo "Received $clientname from the user";
	#gen_cert $clientname;
	#return $clientname;
	case $1 in
		[1]* ) gen_cert $clientname;;
		[2]* ) revoke_cert $clientname;;
		* ) echo "Error in get_client";;
	esac
}

### MAIN ###

while true; do
    echo "1) Generate Certificate";
    echo "2) Revoke Certificate";
    read -p "Selection: " sel
    case $sel in
        [1]* ) get_client $sel; break;;
        [2]* ) get_client $sel; break;;
        [3]* ) exit;;
        * ) echo "Please select 1,2 or 3";;
    esac
done
