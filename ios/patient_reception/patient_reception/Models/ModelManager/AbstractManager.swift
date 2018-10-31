//
//  AbstractManager.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON


class AbstractManager<T>: NSObject {
    
    func request(_ url:String, method:HTTPMethod, params:[String:AnyObject]?, complitionHandler:@escaping (T) -> Void) {
        Alamofire.request(url, method: .get, parameters: params).responseSwiftyJSON { (response) in
            let resultObject = self.createObject()
            resultObject.setData(response: response)
            complitionHandler(resultObject as! T)
        }
    }
    
    
    func createObject() -> AbstractObject {
        return AbstractObject()
    }
    
    
}
