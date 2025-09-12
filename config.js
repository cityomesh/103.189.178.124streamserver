// config.js
// const isLocal = window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1";

// export const MAIN_CDN_URL = isLocal 
//   ? "http://localhost:3000/"   // Local run chesthe idi
//   : "http://10.10.148.25/";    // Server lo run chesthe idi
var SPEEDTEST_SERVERS = [
   {
    name: "Main CDN IP",
    server: "//192.168.12.103/MainCdnServer/",   // correct format
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
  //  {
  //   name: "streamtv",
  //   server: "//103.189.178.121/streamtv/",   // correct format
  //   dlURL: "backend/garbage.php",
  //   ulURL: "backend/empty.php",
  //   pingURL: "backend/empty.php",
  //   getIpURL: "backend/getIP.php"
  // },
    {
    name: "CDN DSN3",
    server: "//10.6.6.165/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
    {
    name: "BLCRDHE EDGECDN1006",
    server: "//10.7.7.252/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
   {
    name: "KAMALAMILLSHATHWAY1007",
    server: "//172.31.42.2/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
    {
    name: "HYDERABAD EDGECDN1008",
    server: "//172.31.32.2/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
   {
    name: "KANPUR EXCITEL",
    server: "//172.29.3.178/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
      {
    name: "Testing",
    server: "//192.168.12.53/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
];

var CLIENTS = [
  { name: "CDN DSN3", ip: "10.6.6.165" },
  { name: "BLCRDHE EDGECDN1006", ip: "10.7.7.252" },
  { name: "KAMALAMILLSHATHWAY1007", ip: "172.31.42.2" },
  { name: "HYDERABAD EDGECDN1008", ip: "172.31.32.2" },
  { name: "KANPUR EXCITEL", ip: "172.29.3.178" },
  { name: "Testing", ip: "192.168.12.53" }
];
