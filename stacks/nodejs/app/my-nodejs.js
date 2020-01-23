/*
Eric Weissgerber

Practicing with REST APIs
Currently familiarizing myself with POST/GET requests and connecting this API to an SQL database

*/

// const http = require('http');
// Const hostname = '0.0.0.0';
//const con = mdb.createPool ({
//var mysql = require('mysql');

const port = 8081;
const express = require('express');
const mdb = require('mariadb');
const app = express();

// Working with MariaDB promises
console.log("dbConnection function called");
const con = mdb.createConnection({
	host: "db",
	user: "root",
	password: "example",
	database: "myapi"
})
.then(con => {
	console.log("Database Connection successful");
})
.catch(err => {
	console.log("Error: " +err.message);
});

var dbquery = new Promise(function (resolve, reject) {
	con.query('Select * from test', function (error, results, fields) {
		if (error) console.log(error);//throw error;
	}).then(results => {
		console.log(results);
	})
});


console.log(con);

/*
con.connect(function(err) {
	if(err){
		console.log(err.code);
	}
});

$query = 'SELECT * FROM test';

con
	.query($query)
	.then(rows => {
		console.log(rows);
	})
	.catch(err => {
		console.log("Error: " +err.message);
	});
*/

// Select query the database
/*con.query('SELECT * FROM test', (err,rows) => {
		if(err) throw err;
		console.log('Data received from DB:');
		console.log(rows);
})
.then(res => {
	console.log(res);
	con.end();
})
.catch(err => { 
	console.log("Error: " +err.message);
});*/





/*
async function asyncFunction() 
{
// Nothing but problems here
	console.log("Async function has been called ... \n");
	let conn;
	try 
	{
		console.log("Attempting to connect to database ... ");
		conn = await con.getConnection();
		console.log("After db connect, confirm connection");
		console.log("Creating database myapi");
		/* Need to confirm we're actually connected to the database */
		/*
		await conn.query("CREATE DATABASE myapi", function (err, result) {
			if (err) throw err;
			console.log(result);
			console.log("Database created\n");
		});
		*/
/*
		console.log("Running SELECT query ...\n");
		const rows = await conn.query("SELECT 1 as val");
		console.log(rows);
/*
		console.log("Running INSERT query ...\n");
		const res = await conn.query("INSERT INTO test value (?, ?)", [2, "mariadb"]);

		console.log(res);
*/
		//console.log("Outputing entire table test");
		//const res2 = await conn.query("SELECT * from test");
		/*
		conn.query("SELECT * from test", (err, res) => {
			console.log(res2);
		}
		*/
/*
	}
	catch(err)
	{
		throw err;
	}
	finally
	{
		if (conn) return conn.end();
	};
}
*/
//con.getConnection() 

app.get('/', (req, res) => {
	res.statuscode = 200;
	res.setHeader('Content-Type', 'text/plain');
	res.send('Hello World\n');
	console.log('Received GET for url /');
//	con.query("SELECT * FROM test", function(error, rows, fields) {
//		console.log("Get /, showing all rows");
//	});
});

app.get('/api/users', (req, res) => {
	res.statuscode = 200;
	res.setHeader('Content-Type', 'text/plain');
	res.send('GET users SUCCESS\n');
	console.log('Received GET for url /api/users');
});

app.get('/api/get', (req, res) => {
	res.statuscode = 200;
	res.setHeader('Content-Type', 'text/plain');
	res.send('GET success\n');
	console.log('Received GET for url /api/get');
});

app.post('/api/users', (req, res) => {
	// Add user to the data structure
	res.statuscode = 301;
	res.setHeader('Content-Type', 'text/plain');
	res.send('POST success\n');
	console.log('Received POST for url /api/users');
});

app.listen(port, () => console.log('Listening on port 8081...'));
// Working on the function to alter the database, currently not being called
//asyncFunction();

/*
con.end(function(){
	console.log("Database connection closed");
});
*/
