/*
 * Copyright (C) 2007 lenny@mondogrigio.cjb.net
 *
 * This file is part of PJBS (http://sourceforge.net/projects/pjbs)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

package pjbs;

import java.io.IOException;
import java.net.ServerSocket;

/**
 * 
 * @author lenny
 */
public class Server extends Thread {
    
    private ServerSocket serverSocket = null;
    private boolean listening = true;
    
    /**
     * Creates a new instance of Server
     */
    public Server() {
        
        int port = 4444;
        String[] drivers = { "org.postgresql.Driver" };
        
        String[] p = Utils.parseFile("../conf/pjbs.conf");
        
        if (p != null && p.length >= 3) {
            
            port = Integer.parseInt(p[1]);
            drivers = new String[p.length - 2];
            
            for (int i = 2; i < p.length; i ++)
                drivers[i - 2] = p[i];
            
        } else {
            
            Utils.log("warning", "invalid config file, using defaults");
        }
        
        try {
            
            serverSocket = new ServerSocket(port);
            Utils.log("notice", "listening on " + port);
            
        } catch (IOException e) {
            
            Utils.log("error", "could not listen on " + port);
            return;
        }
        
        try {
            
            for (int i = 0; i < drivers.length; i ++) {
                
                Class.forName(drivers[i]);
                Utils.log("notice", "loaded " + drivers[i]);
            }
            
        } catch (ClassNotFoundException ex) {
            
            Utils.log("error", "could not load JDBC drivers");
            return;
        }
    }
    
    public void run() {
        
        while (listening) {
            
            try {
                
                new ServerThread(serverSocket.accept()).start();
                
            } catch (IOException ex) {
                
                Utils.log("error", "could not create thread");
                return;
            }
        }
    }
    
    public void shutdown() {
        
        listening = false;
        interrupt();
    }
    
    public static void main(String[] args) {
        
        Server server = new Server();
        
        try {
            
            server.start();
            server.join();
            
        } catch (InterruptedException ex) {
            
            Utils.log("error", "could not join thread");
        }
    }
}
