import React, { useState, useEffect, useRef } from 'react';
import './App.css';
import logoUrl from './assets/logo.png';
import preloaderLogoUrl from './assets/preloader_logo.png';

interface WPPost {
  id: number;
  title: { rendered: string };
  content: { rendered: string };
  excerpt: { rendered: string };
  _embedded?: {
    'wp:featuredmedia'?: Array<{ source_url: string }>;
  };
}

declare global {
  interface Window {
    initMemozor: () => void;
    memozaData: {
      apiUrl: string;
      nonce: string;
      siteUrl: string;
      themeUrl: string;
      isLoggedIn: boolean;
    };
  }
}

const App: React.FC = () => {
  const [posts, setPosts] = useState<WPPost[]>([]);
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(false);
  const [hasMore, setHasMore] = useState(true);
  const [isInitialLoad, setIsInitialLoad] = useState(true);
  const [isMemozorOpen, setIsMemozorOpen] = useState(false);
  
  const observerRef = useRef<IntersectionObserver | null>(null);
  const bottomRef = useRef<HTMLDivElement>(null);
  const feedRef = useRef<HTMLDivElement>(null);

  const fetchPosts = async (pageNum: number) => {
    setLoading(true);
    try {
      const response = await fetch(`${window.memozaData.apiUrl}?_embed&page=${pageNum}&per_page=5`, {
        headers: {
          'X-WP-Nonce': window.memozaData.nonce
        }
      });
      if (response.ok) {
        const data = await response.json();
        if (data.length === 0) {
          setHasMore(false);
        } else {
          setPosts(prev => [...prev, ...data]);
        }
      } else {
        setHasMore(false);
      }
    } catch (error) {
      console.error('Error fetching posts:', error);
      setHasMore(false);
    } finally {
      setLoading(false);
      if (pageNum === 1) {
        setTimeout(() => setIsInitialLoad(false), 500);
      }
    }
  };

  useEffect(() => {
    fetchPosts(1);
  }, []);

  useEffect(() => {
    if (page > 1) {
      fetchPosts(page);
    }
  }, [page]);

  useEffect(() => {
    if (loading || !hasMore) return;
    
    if (observerRef.current) observerRef.current.disconnect();

    observerRef.current = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        setPage(prev => prev + 1);
      }
    }, { 
      root: feedRef.current,
      rootMargin: '200px' 
    });

    if (bottomRef.current) {
      observerRef.current.observe(bottomRef.current);
    }

    return () => observerRef.current?.disconnect();
  }, [loading, hasMore]);

  useEffect(() => {
    if (isMemozorOpen && window.initMemozor) {
      window.initMemozor();
    }
  }, [isMemozorOpen]);

  const handleCreateMeme = () => {
    setIsMemozorOpen(true);
  };

  const handleCloseMemozor = () => {
    setIsMemozorOpen(false);
  };

  const handleLogin = () => {
    if (window.memozaData.isLoggedIn) {
      window.location.href = `${window.memozaData.siteUrl}/wp-login.php?action=logout`;
    } else {
      window.location.href = `${window.memozaData.siteUrl}/wp-login.php`;
    }
  };

  return (
    <div className="app-container">
      {isMemozorOpen && (
        <div className="memozor-modal">
          <button className="memozor-close-btn" onClick={handleCloseMemozor}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
          <div id="memozor-container">
            {/* Honeypot field for bot protection */}
            <input type="text" id="memozor-website-url" name="website_url" style={{ display: 'none' }} tabIndex={-1} autoComplete="off" />
            <div id="memozor-toolbar">
                <input type="file" id="memozor-upload" accept="image/png, image/jpeg, image/webp" title="Upload Image" />
                <button type="button" id="memozor-undo" disabled title="Undo">↶ Undo</button>
                <button type="button" id="memozor-redo" disabled title="Redo">↷ Redo</button>
                <button type="button" id="memozor-add-text">Add Text</button>
                <label>Font: 
                    <select id="memozor-font-family" defaultValue="Impact, sans-serif">
                        <option value="Impact, sans-serif">Impact</option>
                        <option value="Arial, sans-serif">Arial</option>
                        <option value="'Comic Sans MS', cursive">Comic Sans</option>
                        <option value="'Oswald', sans-serif">Oswald</option>
                        <option value="'Anton', sans-serif">Anton</option>
                        <option value="'Bebas Neue', sans-serif">Bebas Neue</option>
                        <option value="'Creepster', cursive">Creepster (Spooky!)</option>
                        <option value="'Press Start 2P', cursive">Press Start 2P (Retro!)</option>
                    </select>
                </label>
                <label>Color: <input type="color" id="memozor-text-color" defaultValue="#ffffff" /></label>
                <label>Outline: <input type="color" id="memozor-stroke-color" defaultValue="#000000" /></label>
                <label>Size: <input type="range" id="memozor-text-size" min="10" max="150" defaultValue="40" /></label>
                <button type="button" id="memozor-save">Save Meme</button>
            </div>
            <div id="memozor-canvas-container">
                <canvas id="memozor-canvas" width="600" height="400"></canvas>
            </div>
            <div id="memozor-message"></div>
          </div>
        </div>
      )}

      <div className={`preloader-overlay ${isInitialLoad ? '' : 'hidden'}`}>
        <img src={preloaderLogoUrl} alt="Loading..." className="preloader-logo" />
      </div>

      <div className="top-nav">
        <a href="/" style={{ display: 'inline-block' }}>
          <img src={preloaderLogoUrl} alt="Memoza" className="logo" />
        </a>
      </div>
      
      <div className="feed-container" ref={feedRef}>
        {posts.map((post) => {
          const imageUrl = post._embedded?.['wp:featuredmedia']?.[0]?.source_url;
          return (
            <div key={post.id} className="post-snap-item">
              <div className="post-content">
                <h2 dangerouslySetInnerHTML={{ __html: post.title.rendered }} />
                {imageUrl && <img src={imageUrl} alt="meme" className="post-image" />}
                {!imageUrl && <div dangerouslySetInnerHTML={{ __html: post.content.rendered }} className="post-text-content" />}
              </div>
              <div className="post-actions-overlay"></div>
            </div>
          );
        })}
        {posts.length > 0 && (
          <div ref={bottomRef} className="loading-indicator">
            {loading && <span>Loading more memes...</span>}
          </div>
        )}
      </div>
      
      <nav className="bottom-nav">
        <button onClick={handleLogin} className="nav-btn">
          {window.memozaData.isLoggedIn ? (
            <>
              <svg viewBox="0 0 24 24" fill="none" stroke="#ff4d4d" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
              <span>Logout</span>
            </>
          ) : (
            <>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>Login</span>
            </>
          )}
        </button>
        <button onClick={handleCreateMeme} className="nav-btn create-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
        <button className="nav-btn">
           <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
           <span>Inbox</span>
        </button>
      </nav>
    </div>
  );
};

export default App;
