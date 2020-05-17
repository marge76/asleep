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
import java.util.Vector;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.queryParser.QueryParser;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;

/**
 *
 * @author lenny
 */
public class Search {
    
    private String partition;
    private IndexWriter indexwriter = null;
    private Vector keys, scores;
    private int matches, cur;
    
    /** Creates a new instance of Search */
    public Search(String pn) {
        
        this.partition = "../var/" + Utils.safeFn(pn);
        this.keys = new Vector();
        this.scores = new Vector();
        this.matches = 0;
        this.cur = -1;
    }
    
    public boolean startIndex() {
        
        try {
            
            indexwriter = new IndexWriter(partition, new StandardAnalyzer(), true);
            return true;
            
        } catch (IOException ex) {
            
            Utils.log("search", "could not open index on partition " + partition);
            return false;
        }
    }
    
    public boolean addDocument(String key, String value) {
        
        try {
            
            Document doc = new Document();
            doc.add(new Field("key", key, Field.Store.YES, Field.Index.UN_TOKENIZED));
            doc.add(new Field("value", value, Field.Store.NO, Field.Index.TOKENIZED));
            indexwriter.addDocument(doc);
            return true;
            
        } catch (IOException ex) {
            
            Utils.log("search", "could not add document on partition " + partition);
            return false;
        }
    }
    
    /**
     * 
     * @return 
     */
    public boolean endIndex() {
        
        try {
            
            indexwriter.optimize();
            indexwriter.close();
            return true;
            
        } catch (IOException ex) {
            
            Utils.log("search", "could not close index on partition " + partition);
            return false;
        }
    }
    
    public boolean query(String s, int off, int len) {
        
        try {
            
            IndexSearcher is = new IndexSearcher(partition);
            QueryParser parser = new QueryParser("value", new StandardAnalyzer());
            
            try {
                
                Query query = parser.parse(s);
                Hits hits = is.search(query);
                
                int start = Math.min(off, hits.length());
                int end = Math.min(start + len, hits.length());
                
                matches = hits.length();
                
                for(int i = start; i < end; i ++) {
                    
                    keys.add(hits.doc(i).get("key"));
                    scores.add(new Integer((int)(hits.score(i) * 100)).toString());
                }
                
                return true;
                
            } catch (ParseException ex) {
                
                Utils.log("search", "could not parse query");
                return false;
            }
            
        } catch (IOException ex) {
            
            Utils.log("search", "could not read index on partition " + partition);
            return false;
        }
    }
    
    public String getKey() {
        
        return (String)keys.get(cur);
    }
    
    public String getScore() {
        
        return (String)scores.get(cur);
    }
    
    public int getCount() {
        
        return keys.size();
    }
    
    public boolean next() {
        
        cur ++;
        
        if (cur < keys.size())
            return true;
        else
            return false;
    }
    
    public int getMatches() {
        
        return matches;
    }    
}
