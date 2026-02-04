// config.js
// const isLocal = window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1";

// export const MAIN_CDN_URL = isLocal 
//   ? "http://localhost:3000/"   // Local run chesthe idi
//   : "http://10.10.148.25/";    // Server lo run chesthe idi
var SPEEDTEST_SERVERS = [
   {
    name: "SIFY CDN",
    server: "//192.168.12.65/",   // correct format
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
    {
    name: "SPIDERLINK JAIPUR",
    server: "//192.168.24.5/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
    {
    name: "SHARPLINK INDORE CDN",
    server: "//192.168.25.5/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
  {
    name: "SPIDERLINK_NOIDA",
    server: "//192.168.27.5/",
    dlURL: "backend/garbage.php",
    ulURL: "backend/empty.php",
    pingURL: "backend/empty.php",
    getIpURL: "backend/getIP.php"
  },
];

var CLIENTS = [
  { name: "SIFY CDN", ip: "192.168.24.5" },
  { name: "SHARPLINK INDORE CDN", ip: "192.168.25.5" },
  { name: "SPIDERLINK_NOIDA", ip: "192.168.27.5" },
];
