function getCurrentDateString()
{
	return (new Date().toISOString() + ' :: ' + 'DEBUG :: ');
};

function logger(msg)
{
	if (!msg) return;
	console.log(getCurrentDateString() + msg);
};

function getInput()
{
	logger("Prompting user for name data");
	var name = prompt("Enter your name");
	logger(name + " received from user");
	logger("Adding " + name + " to the Main Header");
	document.getElementById('MainHeader').innerHTML += " " + name;
	//logger();
};


//logger("First logger data");
